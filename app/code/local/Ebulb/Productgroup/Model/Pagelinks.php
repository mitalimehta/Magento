<?php
   
class Ebulb_Productgroup_Model_Productgroup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productgroup/productgroup');
    }
    
}