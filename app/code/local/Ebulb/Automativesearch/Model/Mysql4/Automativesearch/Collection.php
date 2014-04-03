<?php 

class Ebulb_Automativesearch_Model_Mysql4_Automativesearch_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{ 
    public function _construct()
    {
        parent::_construct();
        
        //$resources = Mage::getSingleton('core/resource');
        
        $this->_init('automativesearch/automativesearch'); 
    
    }
    
   
    
}