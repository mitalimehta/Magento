<?php

class Ebulb_Automaticfeed_Model_Autofeedinfo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('automaticfeed/autofeedinfo');
    }
    
    public function saveAttributes($company_attr,$attribute_id,$custom_value,$sort_order,$modelid){
        return $this->getResource()->saveAttributes($company_attr,$attribute_id,$custom_value,$sort_order,$modelid); 
    }
    
    public function getAttributes($feed_id){
        return $this->getResource()->getAttributes($feed_id);    
    }
    
    public function getcompanyinfo($companyList='',$websiteList=''){
        return $this->getResource()->getcompanyinfo($companyList,$websiteList);    
    }
}
