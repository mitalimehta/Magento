<?php

class Ebulb_Automaticfeed_Model_Company extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('automaticfeed/company');
    }
    
}
