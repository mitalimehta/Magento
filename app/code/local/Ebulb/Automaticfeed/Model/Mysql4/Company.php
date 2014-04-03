<?php

class Ebulb_Automaticfeed_Model_Mysql4_Company extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('automaticfeed/company', 'company_id');
    }
}

?>