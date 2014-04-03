<?php

class Ebulb_Automaticfeed_Model_Mysql4_Company_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('automaticfeed/company');
    } 
}

?>