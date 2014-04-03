<?php

class Ebulb_Purchase_Model_Mysql4_Stockmovement extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    { 
        $this->_init('purchase/stockmovement', 'stock_movement_id');
    }
}

?>