<?php

class Ebulb_Purchase_Model_Mysql4_Orderitem extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('purchase/orderitem', 'order_item_id');
    }
}
?>