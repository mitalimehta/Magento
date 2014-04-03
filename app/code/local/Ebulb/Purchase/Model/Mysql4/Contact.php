<?php

class Ebulb_Purchase_Model_Mysql4_Contact extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('purchase/contact', 'vendor_contact_id');
    }
}

?>