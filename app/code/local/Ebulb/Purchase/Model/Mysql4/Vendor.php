<?php

class Ebulb_Purchase_Model_Mysql4_Vendor extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    { 
        $this->_init('purchase/vendor', 'vendor_id');
    }
} 