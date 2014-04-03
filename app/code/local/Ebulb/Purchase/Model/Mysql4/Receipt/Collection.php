<?php

class Ebulb_Purchase_Model_Mysql4_Receipt_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/receipt');
    }
    
    

    public function getSelect()
    {
        return $this->_select;
    }
    
}