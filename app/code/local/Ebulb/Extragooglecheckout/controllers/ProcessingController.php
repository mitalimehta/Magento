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
 * @category    Ebulb
 * @package     Ebulb_Extragooglecheckout
 * @copyright   Copyright (c) 2009 Ebulb 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ebulb_Extragooglecheckout_ProcessingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
 
    /**
     * Show orderPlaceRedirect page which contains the Extragooglecheckout iframe.
     */
    public function paymentAction()
    {
        try {
            $session = $this->_getCheckout();
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            $session->setExtragooglecheckoutQuoteId($session->getLastQuoteId());
            $session->getQuote()->setIsActive(false)->save();

            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            $order->save();
            $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('extragooglecheckout/payment')
                ->toHtml()
            );
            return $this;
            //$session->clear();
            
        } catch (Exception $e){
            Mage::logException($e);
            parent::_redirect('checkout/cart');
        }
    }
                                                      
    /**
     * Action to which the customer will be returned when the payment is made.
     */
    public function successAction()
    {
        
        try {
            $session = $this->_getCheckout();
            $quoteId = $session->getExtragooglecheckoutQuoteId(true);
            $session->setQuoteId($quoteId);
            $session->getQuote()->setIsActive(false)->save();
            $session->setLastSuccessQuoteId($quoteId);
            $this->_redirect('checkout/onepage/success', array('_secure'=>true));
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch(Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }
    
    public function callbackAction()
    {
        $callback = Mage::getModel('extragooglecheckout/callback');
        $callback->process();
        exit;
    }

    /**
     * Action to which the customer will be returned if the payment process is
     * cancelled.
     * Cancel order and redirect user to the shopping cart.
     */
    public function cancelAction()
    {
        try { 
            $session = $this->_getCheckout();
            $quoteId = $session->getExtragooglecheckoutQuoteId(true);
            $session->setQuoteId($quoteId);
            if($session->getLastRealOrderId()) {
               $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if($order->getId()) 
               $order->cancel()->save();
            $this->_redirect('checkout/cart');
            }
         } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
         } catch(Exception $e){
               Mage::logException($e);
         }
         $this->_redirect('checkout/cart');
        
    }
    
  


    /**
     * Set redirect into responce. This has to be encapsulated in an JavaScript
     * call to jump out of the iframe.
     *
     * @param string $path
     * @param array $arguments
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('extragooglecheckout/redirect')
                ->setRedirectUrl(Mage::getUrl($path, $arguments))
                ->toHtml()
        );
        return $this;
    }
}