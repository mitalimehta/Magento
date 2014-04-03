<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ebulb_Extragooglecheckout_Model_Callback extends Ebulb_Extragooglecheckout_Model_Api_Xml_Callback
{
    
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

        $quoteId            = $this->getData('root/shopping-cart/merchant-private-data/quote-id/VALUE');
        $storeId            = $this->getData('root/shopping-cart/merchant-private-data/store-id/VALUE');
        $builtinCartPayment = $this->getData('root/shopping-cart/merchant-private-data/builtin-cart-payment/VALUE');
        $orderId            = $this->getData('root/shopping-cart/merchant-private-data/order-id/VALUE');
                 
        if(!$builtinCartPayment)
            return parent::_responseNewOrderNotification();
        
        if($orderId){
           $order = Mage::getModel('sales/order');
           $order->loadByIncrementId($orderId);
        }
        else
            return parent::_responseNewOrderNotification();
        
        $order->setExtOrderId($this->getGoogleOrderNumber());
        $order->setExtCustomerId($this->getData('root/buyer-id/VALUE'));
        
        $emailAllowed = ($this->getData('root/buyer-marketing-preferences/email-allowed/VALUE')==='true');
        
        $order->addStatusToHistory(
            $order->getStatus(),
            $this->__('Google Order Number: %s', '<strong>'.$this->getGoogleOrderNumber()).'</strong>'.
            '<br />'.
            $this->__('Google Buyer Id: %s', '<strong>'.$this->getData('root/buyer-id/VALUE').'</strong>').
            '<br />'.
            $this->__('Is Buyer Willing To Receive Marketing E-Mails: %s', '<strong>' . ($emailAllowed ? $this->__('Yes') : $this->__('No')) . '</strong>')
        );
        
        $order->place();
        $payment = Mage::getModel('sales/order_payment')->setMethod('googlecheckout');
        $order->setPayment($payment); 
        
        $order->save();
        $order->sendNewOrderEmail();
        
        if ($emailAllowed) 
            Mage::getModel('newsletter/subscriber')->subscribe($order->getCustomerEmail());
        $this->getGRequest()->SendMerchantOrderNumber($order->getExtOrderId(), $orderId);
    } 
    
    /**
     * Import totals information from google request to quote address
     *
     * @param Varien_Object $qAddress
     */
    protected function _importGoogleTotals($qAddress)
    {
        $quote = $qAddress->getQuote();
        $qAddress->setTaxAmount(
            $this->_reCalculateToStoreCurrency($this->getData('root/order-adjustment/total-tax/VALUE'), $quote)
        );
        $qAddress->setBaseTaxAmount($this->getData('root/order-adjustment/total-tax/VALUE'));

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

        if(Mage::helper("extragooglecheckout")->getShippingCodeByDescription($shipping['shipping-name']['VALUE']))
           $method = Mage::helper("extragooglecheckout")->getShippingCodeByDescription($shipping['shipping-name']['VALUE']);
        
        if (!empty($method)) {
            Mage::getSingleton('tax/config')->setShippingPriceIncludeTax(false);
            
            $rate = Mage::getModel('sales/quote_address_rate')
                ->setCode($method)
                ->setPrice($shipping['shipping-cost']['VALUE']);
                
            $qAddress->addShippingRate($rate)
                ->setShippingMethod($method)
                ->setShippingDescription($shipping['shipping-name']['VALUE']);

            /*if (!Mage::helper('tax')->shippingPriceIncludesTax($quote->getStore())) {
                $includingTax = Mage::helper('tax')->getShippingPrice($excludingTax, true, $qAddress, $quote->getCustomerTaxClassId());
                $shippingTax = $includingTax - $excludingTax;
                $qAddress->setShippingTaxAmount($this->_reCalculateToStoreCurrency($shippingTax, $quote))
                    ->setBaseShippingTaxAmount($shippingTax)
                    ->setShippingInclTax($includingTax)
                    ->setBaseShippingInclTax($this->_reCalculateToStoreCurrency($includingTax, $quote));
            } else {
                if ($method == 'googlecheckout_carrier') {
                    $qAddress->setShippingTaxAmount(0)
                        ->setBaseShippingTaxAmount(0);
                }
            }*/
        } else {
            $qAddress->setShippingMethod(null);           
        }


        $qAddress->setGrandTotal(
            $this->_reCalculateToStoreCurrency($this->getData('root/order-total/VALUE'), $quote)
        );
        $qAddress->setBaseGrandTotal($this->getData('root/order-total/VALUE'));
    }
    
    /**
    * Process charge notification
    *
    */
    protected function _responseChargeAmountNotification()
    {
        $this->getGResponse()->SendAck();
        $order = $this->getOrder();
        $payment = $order->getPayment();
        $latestCharged = $this->getData('root/latest-charge-amount/VALUE');
        $totalCharged = $this->getData('root/total-charge-amount/VALUE');
        $payment->setAmountCharged($totalCharged);
        $order->setIsInProcess(true); 
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true,"Order status set to PROCESSING"); 
        $order->setStatus("processing");  
        $msg = $this->__('Google Charge:');
        $msg .= '<br />'.$this->__('Latest Charge: %s', '<strong>' . $this->_formatAmount($latestCharged) . '</strong>');
        $msg .= '<br />'.$this->__('Total Charged: %s', '<strong>' . $this->_formatAmount($totalCharged) . '</strong>');
        if (!$order->hasInvoices() && abs($order->getGrandTotal()-$latestCharged)<.01) {
            $invoice = $this->_createInvoice();
            $msg .= '<br />'.$this->__('Invoice Auto-Created: %s', '<strong>'.$invoice->getIncrementId().'</strong>');
        }
        foreach ($order->getInvoiceCollection() as $orderInvoice) {
            $open = Mage_Sales_Model_Order_Invoice::STATE_OPEN;
            $paid = Mage_Sales_Model_Order_Invoice::STATE_PAID;
            if ($orderInvoice->getState() == $open && $orderInvoice->getGrandTotal() == $latestCharged) {
                $orderInvoice->setState($paid)->save();
                break;
            }
        }
        $order->addStatusToHistory($order->getStatus(), $msg);
        $order->save();
    }
}
