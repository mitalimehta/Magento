<?php

class Ebulb_Purchase_Model_Mysql4_Receiptitem extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('purchase/receiptitem', 'receipt_item_id');
    }
}
?>