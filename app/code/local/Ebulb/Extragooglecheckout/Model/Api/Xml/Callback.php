<?php
class Ebulb_Extragooglecheckout_Model_Api_Xml_Callback extends Mage_GoogleCheckout_Model_Api_Xml_Callback {

    private function logGoogle($message){
        //Mage::log('===Google Checkout==='.$message);
    }
        /**
     * Process notification from google
     * @return Mage_GoogleCheckout_Model_Api_Xml_Callback
     */
    public function process()
    {
        //$this->logGoogle('begin processing..');
        // Retrieve the XML sent in the HTTP POST request to the ResponseHandler
        $xmlResponse = isset($GLOBALS['HTTP_RAW_POST_DATA']) ?  $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");                             

        if (get_magic_quotes_gpc()) {
            $xmlResponse = stripslashes($xmlResponse);
        }

        $debugData = array('request' => $xmlResponse, 'dir' => 'in');

        if (empty($xmlResponse)) {
            $this->getApi()->debugData($debugData);
            return false;
        }

        list($root, $data) = $this->getGResponse()->GetParsedXML($xmlResponse);

        $this->getGResponse()->SetMerchantAuthentication($this->getMerchantId(), $this->getMerchantKey());
        $status = $this->getGResponse()->HttpAuthentication();

        if (!$status || empty($data[$root])) {
            exit;
        }

        $this->setRootName($root)->setRoot($data[$root]);
        $serialNumber = $this->getData('root/serial-number');
        $this->getGResponse()->setSerialNumber($serialNumber);

        /*
         * Prevent multiple notification processing
         */
        $notification = Mage::getModel('googlecheckout/notification')
            ->setSerialNumber($serialNumber)
            ->loadNotificationData();

        if ($notification->getStartedAt()) {
            if ($notification->isProcessed()) {
                $this->getGResponse()->SendAck();
                return;
            }
            if ($notification->isTimeout()) {
                $notification->updateProcess();
            } else {
                $this->getGResponse()->SendServerErrorStatus();
                return;
            }
        } else {
            $notification->startProcess();
        }

        $method = '_response'.uc_words($root, '', '-');
        if (method_exists($this, $method)) {
            ob_start();
             
            try {
                $this->$method();
                $notification->stopProcess();
            } catch (Exception $e) {
                $this->getGResponse()->log->logError($e->__toString());
            }

            $debugData['result'] = ob_get_flush();
            $this->getApi()->debugData($debugData);
        } else {
            $this->getGResponse()->SendBadRequestStatus("Invalid or not supported Message");
        }

        return $this;
    }

    /**
     * Load quote from request and make sure the proper payment method is set
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _loadQuote()
    {
        $quoteId = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        $storeId = $this->getData('root/shopping-cart/merchant-private-data/store-id/VALUE');
        $quote = Mage::getModel('sales/quote')
            ->setStoreId($storeId)
            ->load($quoteId);
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod('extragooglecheckout');
        } else {
            $quote->getShippingAddress()->setPaymentMethod('extragooglecheckout');
        }
        return $quote;
    }
    
        /**
     * Process new order creation notification from google.
     * Convert customer quote to order
     */
    protected function _responseNewOrderNotification()
    {
        $this->getGResponse()->SendAck();

        // LOOK FOR EXISTING ORDER TO AVOID DUPLICATES
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('ext_order_id', $this->getGoogleOrderNumber());
        if (count($orders)) {
            return;
        }

        // IMPORT GOOGLE ORDER DATA INTO QUOTE
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $this->_loadQuote();
        $quote->setIsActive(true)->reserveOrderId();
        $storeId = $quote->getStoreId();
            
        Mage::app()->setCurrentStore(Mage::app()->getStore($storeId));
        if ($quote->getQuoteCurrencyCode() != $quote->getBaseCurrencyCode()) {
            Mage::app()->getStore()->setCurrentCurrencyCode($quote->getQuoteCurrencyCode());
        }

        $billing = $this->_importGoogleAddress($this->getData('root/buyer-billing-address'));
        $quote->setBillingAddress($billing);

        $shipping = $this->_importGoogleAddress($this->getData('root/buyer-shipping-address'));

        $quote->setShippingAddress($shipping);

        $this->_importGoogleTotals($quote->getShippingAddress());

        $quote->getPayment()->importData(array('method'=>'googlecheckout'));

        $taxMessage = $this->_applyCustomTax($quote->getShippingAddress());

        // CONVERT QUOTE TO ORDER
        $convertQuote = Mage::getSingleton('sales/convert_quote');

        /* @var $order Mage_Sales_Model_Order */
        $order = $convertQuote->toOrder($quote);

        if ($quote->isVirtual()) {
            $convertQuote->addressToOrder($quote->getBillingAddress(), $order);
        } else {
            $convertQuote->addressToOrder($quote->getShippingAddress(), $order);
        }

        $order->setExtOrderId($this->getGoogleOrderNumber());
        $order->setExtCustomerId($this->getData('root/buyer-id/VALUE'));
        
        $order->setShippingDescription($this->getData('root/order-adjustment/shipping/merchant-calculated-shipping-adjustment/shipping-name/VALUE'));

        if (!$order->getCustomerEmail()) {
            $order->setCustomerEmail($billing->getEmail())
                ->setCustomerPrefix($billing->getPrefix())
                ->setCustomerFirstname($billing->getFirstname())
                ->setCustomerMiddlename($billing->getMiddlename())
                ->setCustomerLastname($billing->getLastname())
                ->setCustomerSuffix($billing->getSuffix());
        }

        //Create Customer.
        $googleCustomer = $this->createNewCustomerFromGoogleCheckout($quote, $billing);
        if ($googleCustomer) {
            $order->setCustomer($googleCustomer);
        }
        
        $order->setBillingAddress($convertQuote->addressToOrderAddress($quote->getBillingAddress()));
        if (!$quote->isVirtual()) {
            $order->setShippingAddress($convertQuote->addressToOrderAddress($quote->getShippingAddress()));
        }
        #$order->setPayment($convertQuote->paymentToOrderPayment($quote->getPayment()));

        foreach ($quote->getAllItems() as $item) {
            $orderItem = $convertQuote->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }
        
        $order = $this->importGoogleShippingAmount($order);
        
        /*
         * Adding transaction for correct transaction information displaying on order view at back end.
         * It has no influence on api interaction logic.
         */
        $payment = Mage::getModel('sales/order_payment')
            ->setMethod('googlecheckout')
            ->setTransactionId($this->getGoogleOrderNumber())
            ->setIsTransactionClosed(false);
        $order->setPayment($payment);
        $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        //$order->setCanShipPartiallyItem(false);
        $order->setCanShipPartiallyItem(true);

        $emailAllowed = ($this->getData('root/buyer-marketing-preferences/email-allowed/VALUE')==='true');

        $emailStr = $emailAllowed ? $this->__('Yes') : $this->__('No');
        $message = $this->__('Google Order Number: %s', '<strong>'.$this->getGoogleOrderNumber()).'</strong><br />'.
            $this->__('Google Buyer ID: %s', '<strong>'.$this->getData('root/buyer-id/VALUE').'</strong><br />').
            $this->__('Is Buyer Willing to Receive Marketing Emails: %s', '<strong>' . $emailStr . '</strong>');
        if ($taxMessage) {
            $message .= $this->__('<br />Warning: <strong>%s</strong><br />', $taxMessage);
        }

        $order->addStatusToHistory($order->getStatus(), $message);
        $order->place();
        $order->save();
        $order->sendNewOrderEmail();

        $quote->setIsActive(false)->save();

        if ($emailAllowed) {
            Mage::getModel('newsletter/subscriber')->subscribe($order->getCustomerEmail());
        }

        Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $quote));

        $this->getGRequest()->SendMerchantOrderNumber($order->getExtOrderId(), $order->getIncrementId());
    }
    protected function _responseAuthorizationAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $payment->setAmountAuthorized($this->getData('root/authorization-amount/VALUE'));

        $expDate = $this->getData('root/authorization-expiration-date/VALUE');
        $expDate = new Zend_Date($expDate);
        $msg = $this->__('Google Authorization:');
        $msg .= '<br />'.$this->__('Amount: %s', '<strong>' . $this->_formatAmount($payment->getAmountAuthorized()) . '</strong>');
        $msg .= '<br />'.$this->__('Expiration: %s', '<strong>' . $expDate->toString() . '</strong>');

        $order->addStatusToHistory($order->getStatus(), $msg);

        $order->setPaymentAuthorizationAmount($payment->getAmountAuthorized());
        $order->setPaymentAuthorizationExpiration(Mage::getModel('core/date')->gmtTimestamp($this->getData('root/authorization-expiration-date/VALUE')));
        //$order->setTotalPaid($payment->getAmountAuthorized());
        //$order->setBaseTotalPaid($payment->getAmountAuthorized());
        $order->save();
    }
    
    protected function _responseChargeAmountNotification()
    {
        $this->getGResponse()->SendAck();

        $order = $this->getOrder();
        $payment = $order->getPayment();

        $latestCharged = $this->getData('root/latest-charge-amount/VALUE');
        $totalCharged = $this->getData('root/total-charge-amount/VALUE');
        $payment->setAmountCharged($totalCharged);
        $order->setIsInProcess(true);

        $msg = $this->__('Google Charge:');
        $msg .= '<br />'.$this->__('Latest Charge: %s', '<strong>' . $this->_formatAmount($latestCharged) . '</strong>');
        $msg .= '<br />'.$this->__('Total Charged: %s', '<strong>' . $this->_formatAmount($totalCharged) . '</strong>');

        if (!$order->hasInvoices() && abs($order->getBaseGrandTotal() - $latestCharged)<.0001) {
            $invoice = $this->_createInvoice();
            $msg .= '<br />'.$this->__('Invoice Auto-Created: %s', '<strong>'.$invoice->getIncrementId().'</strong>');
        }

        $this->_addChildTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);

        $open = Mage_Sales_Model_Order_Invoice::STATE_OPEN;
        foreach ($order->getInvoiceCollection() as $orderInvoice) {
            if ($orderInvoice->getState() == $open && $orderInvoice->getBaseGrandTotal() == $latestCharged) {
                $orderInvoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID)
                    ->setTransactionId($this->getGoogleOrderNumber())
                    ->save();
                break;
            }
        }

        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();
    }

    
    protected function importGoogleShippingAmount($order)
    {
        
        $prefix = 'root/order-adjustment/shipping/';
        if ($shipping = $this->getData($prefix.'carrier-calculated-shipping-adjustment')) {
            $method = 'googlecheckout_carrier';
        } elseif ($shipping = $this->getData($prefix.'merchant-calculated-shipping-adjustment')) {
            $method = 'googlecheckout_merchant';
        } elseif ($shipping = $this->getData($prefix.'flat-rate-shipping-adjustment')) {
            $method = 'googlecheckout_flatrate';
        } elseif ($shipping = $this->getData($prefix.'pickup-shipping-adjustment')) {
            $method = 'googlecheckout_pickup';
        }
        if (!empty($method)) {
            $order->setShippingAmount($shipping['shipping-cost']['VALUE']);
            $order->setBaseShippingAmount($shipping['shipping-cost']['VALUE']);
        
        }
        return $order;


    }
        
    private function createNewCustomerFromGoogleCheckout($quoteInfo, $billingInfo){
        $this->logGoogle('begin to create new customer');
        
        $defaultPhone = '1-888-505-2111';
        $defaultPassword = 'bulbamerica.com777';
        
        if (!$customer = Mage::registry('current_customer')) {
            $customer = Mage::getModel('customer/customer')->setId(null);
        }
        
        
        $this->logGoogle('billing email='.$billingInfo->getEmail());        
        $customer->setPassword($defaultPassword);
        $customer->setConfirmation($defaultPassword);
        $customer->setData('website_id', Mage::app()->getStore(true)->getWebsiteId());
        $customer->setData('email', $this->getEmail($billingInfo->getEmail()));
        $customer->setData('prefix', $billingInfo->getPrefix());
        $customer->setData('firstname', $billingInfo->getFirstname());
        $customer->setData('middlename', $billingInfo->getMiddlename());
        $customer->setData('lastname', $billingInfo->getLastname());
        $customer->setData('group_id', 1); //hard-code general group =1
        $customer->setData('hearabout', 664); //hard-code Google Direct Checkout
        $customer->setIsSubscribed(0);

        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        $addressBilling = $quoteInfo->getBillingAddress()->exportCustomerAddress()->setIsDefaultBilling(true);
        If (!$addressBilling->getTelephone()){
            $addressBilling->setTelephone($defaultPhone);
        }
        $customer->addAddress($addressBilling);
        $errors = $addressBilling->validate();
        if (!is_array($errors)) {
            $errors = array();
        }
        $addressShipping =  $quoteInfo->getShippingAddress()->exportCustomerAddress()->setIsDefaultShipping(true);
        $customer->addAddress($addressShipping);
        $errors_addressShipping = $addressShipping->validate();
        if (is_array($errors_addressShipping)) {
            $errors = array_merge($errors_addressShipping, $errors);
        }

        try {
            $validationCustomer = $customer->validate();
            if (is_array($validationCustomer)) {
                $errors = array_merge($validationCustomer, $errors);
            }
            $validationResult = count($errors) == 0;

            if (true === $validationResult) {
                $this->logGoogle('No error.');
                $tempCustomer = Mage::getModel('customer/customer')->loadByEmail($customer->getEmail());
                if ($tempCustomer->getId()){
                    return $tempCustomer;
                }else {
                    $customer->save();
                    return $customer;
                }
                
            } else {
                $this->logGoogle('Find error.');
                if (is_array($errors)) {
                    foreach ($errors as $errorMessage) {
                        //$session->addError($errorMessage);
                        $this->logGoogle('Error = '.$errorMessage);
                    }
                }
                else {
                    //$session->addError($this->__('Invalid customer data'));
                    $this->logGoogle('Error = '.$this->__('Invalid customer data')); 
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = Mage::getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
            }
            else {
                $message = $e->getMessage();
            }
            $this->logGoogle('Error = '.$message);
        }
        catch (Exception $e) {
//                $session->setCustomerFormData($this->getRequest()->getPost())
//                    ->addException($e, $this->__('Cannot save the customer.'));
            $this->logGoogle('Error = '.$this->__('Cannot save the customer.'));
        }
        $this->logGoogle('end create new customer');
        return ;
    }
    
    public function getEmail($oldEmail){
        if ( strrpos($oldEmail, "@") === false ){
            $oldEmail = Mage::getModel('noemailgen/noemail')->getNoemail();
        }
        return $oldEmail;
    }

    protected function _orderStateChangeFulfillmentDelivered()
    {
     //disable google callback.
    }    

    protected function _responseOrderStateChangeNotification()
    {
        $this->getGResponse()->SendAck();

        $prevFinancial = $this->getData('root/previous-financial-order-state/VALUE');
        $newFinancial = $this->getData('root/new-financial-order-state/VALUE');
        $prevFulfillment = $this->getData('root/previous-fulfillment-order-state/VALUE');
        $newFulfillment = $this->getData('root/new-fulfillment-order-state/VALUE');

        $msg = $this->__('Google Order Status Change:');
        if ($prevFinancial!=$newFinancial) {
            $msg .= "<br />".$this->__('Financial: %s -> %s', '<strong>'.$prevFinancial.'</strong>', '<strong>'.$newFinancial.'</strong>');
        }
        if ($prevFulfillment!=$newFulfillment) {
            $msg .= "<br />".$this->__('Fulfillment: %s -> %s', '<strong>'.$prevFulfillment.'</strong>', '<strong>'.$newFulfillment.'</strong>');
        }
        
        //Try 3 times, total 15 seconds to confirm the cluster is sync-ed.
        for($i =0 ; $i < 3; $i++){
            if (strtoupper('complete') == strtoupper($this->getOrder()->getStatus())) {
                break;
            }else{
                sleep(5);
            }
        }
        if (strtoupper('complete') != strtoupper($this->getOrder()->getStatus())) {
           $msg .='.'.$this->getOrder()->getStatus(); 
        }
        
        $this->getOrder()
            ->addStatusToHistory($this->getOrder()->getStatus(), $msg)
            ->save();

        $method = '_orderStateChangeFinancial'.uc_words(strtolower($newFinancial), '', '_');
        if (method_exists($this, $method)) {
            $this->$method();
        }

        $method = '_orderStateChangeFulfillment'.uc_words(strtolower($newFulfillment), '', '_');
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }    
    
}  
?>
