<?php

class Ebulb_Purchase_Model_Mysql4_Invoice extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('purchase/invoice', 'invoice_id');
    }
}
?>