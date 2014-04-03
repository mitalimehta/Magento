<?php

class Ebulb_Purchase_Model_Mysql4_Receiptitem_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/receiptitem');
    }
    
    

    public function getSelect()
    {
        return $this->_select;
    }
    
}