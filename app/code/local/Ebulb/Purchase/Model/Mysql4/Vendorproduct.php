<?php

class Ebulb_Purchase_Model_Mysql4_Vendorproduct extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('purchase/vendorproduct', 'vendor_product_id');
    }
}
?>