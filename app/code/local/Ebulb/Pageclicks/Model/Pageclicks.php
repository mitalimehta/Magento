<?php
   
class Ebulb_Pageclicks_Model_Pageclicks extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pageclicks/pageclicks');
    }
    
}