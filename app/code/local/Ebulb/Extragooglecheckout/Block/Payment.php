<?php
/**
 * Magento
 *
 * @category    Ebulb
 * @package     Ebulb_Extragoogle checkout
 * @copyright   Copyright (c) 2010 eBulb (www.bulbamerica.com)
 * @author      Augusto Leao
 */
class Ebulb_Extragooglecheckout_Block_Payment extends Mage_Core_Block_Template
{
     
    protected $_cartItemXml = array();
    protected $_privateData;
    protected $_continueShoppingUrl;
    protected $_editCartUrl;
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('extragooglecheckout/payment.phtml');
    }
    
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function getQuote(){
        
        return Mage::getSingleton('checkout/session')->getQuote();
        
    }
    
    public function getQuoteId()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getId();
    }
    
    public function getOrderId()
    {
        return $this->getCheckout()->getLastRealOrderId();
    }
    
    public function getCartItems()
    {
        $orderDetails = Mage::getModel ( 'sales/order' )->loadByIncrementId ( $this->getOrderId() );
        return $orderDetails->getAllItems();        
        //getAllItems() 
        //return $items = $this->getCheckout()->getQuote()->getItemsCollection();
    }
    
    public function getOrder(){
        
        return Mage::getModel ( 'sales/order' )->loadByIncrementId ( $this->getOrderId() );
    }
 
    
    public function getCurrencyCode()
    {
        
        return Mage::app()->getStore()->getCurrentCurrencyCode();
        
    }
    
    public function getTaxTotal()
    {      
           return round((float)$this->getOrder()->getBaseTaxAmount(),2);                                         
           
    }
    
    public function getShippingTotal()
    {
                                                                                           
          return round((float)$this->getOrder()->getBaseShippingAmount(),2);
    }
    
    public function getDiscount()                       
    {
        
        $discount = $this->getOrder()->getBaseDiscountAmount();
        $description = $this->getOrder()->getDiscountDescription();
        
        if($discount)
           return new Varien_Object(array(
                        'code'  => $this->__('_INTERNAL_DISCOUNT_'),
                        'price' => $discount,
                        'name'  => $this->__('Cart Discount'),
                        'description' => $this->__($description)
                        ));
        else 
          return false;
                             
        /** if ($discount) {
        $this->_cartItemXml[] = "<item>
                             <merchant-item-id>_INTERNAL_DISCOUNT_</merchant-item-id>
                             <item-name>Cart Discount</item-name>
                             <item-description>$description</item-description>
                             <unit-price currency=\"{$this->getCurrencyCode()}\">{$discount}</unit-price>
                             <quantity>1</quantity>
                             <tax-table-selector>none</tax-table-selector>
                             </item>";
        }**/
    
    }         
    
    public function getShippingDescription()
    {
        foreach($this->getCheckout()->getQuote()->getTotals() as $total)
                  if($total->getCode()=="shipping")
                   return $total->getTitle();
        
    }
    
    public function getButtonUrl()
    {
        $url = 'https://checkout.google.com/buttons/checkout.gif';
        $url .= '?merchant_id='.Mage::getStoreConfig('google/checkout/merchant_id');
        $v = $this->getImageStyle();
        $url .= '&w='.$v[0].'&h='.$v[1].'&style='.$v[2];
        $url .= '&variant='.($this->getIsDisabled() ? 'disabled' : 'text');
        $url .= '&loc='.Mage::getStoreConfig('google/checkout/locale');
        return $url;
    }
    
    public function getImageStyle()
    {
        $s = Mage::getStoreConfig('google/checkout/checkout_image');
        if (!$s) {
            $s = '180/46/trans';
        }
        return explode('/', $s);
    }
    
    public function setItemXml($sku,$name,$description,$price,$qty)
    {
        $this->_cartItemXml[] = "<item>
                             <merchant-item-id>$sku</merchant-item-id>  
                             <item-name>$name</item-name>
                             <item-description>$description</item-description>
                             <unit-price currency=\"".$this->getCurrencyCode()."\">$price</unit-price>
                             <quantity>$qty</quantity>
                             </item>";
    }
    
    public function setPrivateData($var)
    {
        $this->_privateData = $var;
    }
    
    public function setContinueShoppingUrl($url)
    {
        $this->_continueShoppingUrl = $url;
    }
    
    public function setEditCartUrl($url)
    {
        $this->_editCartUrl = $url;
    }
    
    
    public function getBodyXml($encoded = true)
    {
        
       $xml ='<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">
              <shopping-cart><items>'.join("",$this->_cartItemXml).'</items>';
       $xml.='<merchant-private-data>
              <quote-id>'.$this->getQuoteId().'</quote-id>
              <order-id>'.$this->getOrderId().'</order-id>
              <builtin-cart-payment>1</builtin-cart-payment>
              <store-id>'.Mage::app()->getStore()->getId().'</store-id>
              </merchant-private-data>';                                                                                                         
       $xml.='</shopping-cart>
              <checkout-flow-support>
              <merchant-checkout-flow-support>
              <edit-cart-url>'.$this->_editCartUrl.'</edit-cart-url>
              <continue-shopping-url>'.$this->_continueShoppingUrl.'</continue-shopping-url>
              <tax-tables>                                             
              <default-tax-table>
              <tax-rules>
              <default-tax-rule>
              <shipping-taxed>false</shipping-taxed>
              <rate>0</rate>
              <tax-area>                                                                 
              <us-state-area>
              <state>AK</state>
              </us-state-area>
              </tax-area>
              </default-tax-rule>
              </tax-rules>
              </default-tax-table>
              </tax-tables>
              <rounding-policy>
              <mode>HALF_UP</mode>
              <rule>PER_LINE</rule>
              </rounding-policy>
              </merchant-checkout-flow-support>
              </checkout-flow-support>
              </checkout-shopping-cart>';
    
    if($encoded)
       return base64_encode($xml);
    else  
       return $xml;
    }
    
    public function getSignature($encoded = true){
        
      $key = Mage::getStoreConfig('google/checkout/merchant_key');
      $data = $this->getBodyXml(false);
      
      $blocksize = 64;
      $hashfunc = 'sha1';
      if (strlen($key) > $blocksize) {
        $key = pack('H*', $hashfunc($key));
      }
      $key  = str_pad($key, $blocksize, chr(0x00));
      $ipad = str_repeat(chr(0x36), $blocksize);
      $opad = str_repeat(chr(0x5c), $blocksize);
      $hmac = pack(
                    'H*', $hashfunc(
                            ($key^$opad).pack(
                                    'H*', $hashfunc(
                                            ($key^$ipad).$data
                                    )
                            )
                    )
                );
      return $encoded ? base64_encode($hmac) : $hmac ; 
    }
    
    
    public function getCheckOutUrl()
    {
        $url = $this->_getBaseApiUrl(). Mage::getStoreConfig('google/checkout/merchant_id') ;
        return $url;
    }
    
    protected function _getBaseApiUrl()
    {
        $url = 'https://';
        if ($this->getServerType()=='sandbox') {
            $url .= 'sandbox.google.com/checkout/api/checkout/v2/checkout/Merchant/';
        } else {
            $url .= 'checkout.google.com/api/checkout/v2/checkout/Merchant/';                
        }
        return $url;
    }
    
    public function getServerType()
    {
        if (!$this->hasData('server_type')) {
            $this->setData('server_type', Mage::getStoreConfig('google/checkout/sandbox', $this->getStoreId()) ? "sandbox" : "");
        }
        return $this->getData('server_type');
    }
    
    public function getMessage ()
    {
        return $this->__('You will be redirected to GoogleCheckout in a few seconds.');
    }
    
}
