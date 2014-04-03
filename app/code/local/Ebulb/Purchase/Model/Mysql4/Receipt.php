<?php

class Ebulb_Purchase_Model_Mysql4_Receipt extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('purchase/receipt', 'receipt_id');
    }
}
?>