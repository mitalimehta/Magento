<?php

class Ebulb_Purchase_Model_Product extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/product');
    }
    
    public function checkexistingproduct(){  
        return $this->getResource()->checkexistingproduct($this->getData('product_id'),$this->getData('vendor_id'),$this->getData('vendor_product_id')); 
    }
}
