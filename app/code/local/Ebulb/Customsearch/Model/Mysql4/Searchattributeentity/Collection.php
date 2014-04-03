<?php

class Ebulb_Customsearch_Model_Mysql4_Searchattributeentity_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customsearch/searchattributeentity');
    } 
}
