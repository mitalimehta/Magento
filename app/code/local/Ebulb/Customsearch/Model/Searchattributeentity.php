<?php

class Ebulb_Customsearch_Model_Searchattributeentity extends Mage_Core_Model_Abstract
{
    public $_searchTypeIds = array
        (  
            '1' => 'Attribute Set',
            '2' => 'Attribute'
        );
        
    public $_status = array
        (  
            '1' => 'Enabled',
            '2' => 'Disabled'
        );    
        
        
    public $_categories=null;
    
    const CATEGORIES_ARRAY_CACHE_KEY = 'categories_array_cache_key';
        
    public function _construct()
    {
        parent::_construct();
        $this->_init('customsearch/searchattributeentity');
    }
    
    public function getsearchTypeIds(){
        return $this->_searchTypeIds;
    }
    
    public function getstatusIds(){
        return $this->_status;
    }
    
    public function saveSearchAttributes($attributes,$attributes_sort_order,$entity_id){
        return $this->getResource()->saveSearchAttributes($attributes,$attributes_sort_order,$entity_id); 
    }
    
    public function getSearchAttributes($entity_id){    
        return $this->getResource()->getSearchAttributes($entity_id); 
    }
    
    public function getProjectorSearchEntityID(){
        return $this->getResource()->getProjectorSearchEntityID();     
    }
    
    public function loadbyTypeEntityid($type_entity_id = null){
        return $this->getResource()->loadbyTypeEntityid($type_entity_id);         
    }
    
    public function checkExistingRecord($entity_id=null,$type_id,$type_entity_id){
        return $this->getResource()->checkExistingRecord($entity_id,$type_id,$type_entity_id);     
    }  
    
    public function get_categories(){
        if(!is_array($this->_categories)){
            //$cache = Mage::getSingleton('core/cache');
            
            if(Mage::getSingleton('core/session')->getcategoriestree()){
                $this->_categories = Mage::getSingleton('core/session')->getcategoriestree();  
            }
            else{
            //    $adminSessionLifetime = (int)Mage::getStoreConfig('admin/security/session_cookie_lifetime'); 
                  $this->_categories = Mage::getModel('adminhtml/system_config_source_category')->toOptionArray(); 
                  Mage::getSingleton('core/session')->setcategoriestree($this->_categories);
                //Mage::app()->saveCache($this->_categories, self::CATEGORIES_ARRAY_CACHE_KEY,array(),$adminSessionLifetime);   
            //    $cache->save($this->_categories,self::CATEGORIES_ARRAY_CACHE_KEY,$this->_categories,$adminSessionLifetime);
            }
        }
        
        return $this->_categories;
    }
    
    public function getCurrentSearch($Category_id = null){
        return $this->getResource()->getCurrentSearch($Category_id);            
    }
    
}

