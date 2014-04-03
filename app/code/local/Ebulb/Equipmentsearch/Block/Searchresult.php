<?php

class Ebulb_Equipmentsearch_Block_Searchresult extends Mage_Catalog_Block_Product_List{ 
     
    protected $_manufacturer;
    protected $_year;
    protected $_model;
    protected $_type;  
    protected $_location; 
    
    protected $_productCollection;  
   
    protected $_manufactuerName;
    protected $_yearName;
    protected $_modelName;
    protected $_typeName;
    protected $_locationName;
    protected $_equipmentid;
    
    protected $_storeid;
   
    public function __construct(){
    }
   
    public function _getProductCollection()
    {
        
        $this->_storeid = Mage::app()->getStore()->getId();
        
        $this->_equipmentid = $this->equipmentid; 
        $this->_equipmentid = 3731; 
        
        $this->_productlist   = Mage::getModel('equipmentsearch/equipmentsearch')
                                      ->getProductCollection($this->_equipmentid);
        $productarray = array();
        
        foreach($this->_productlist as $key=>$val){  
               $productarray[] = $val['product_id']; 
        }
          
        $collection = Mage::getSingleton('catalog/product')->getCollection()
                         ->addAttributeToFilter("entity_id",array("in"=>$productarray))
                         ->addAttributeToSelect('*')
                         ->setStoreId($this->_storeid)
                         ->load();  
        
        //echo "<pre>";print_r($collection);exit;
        
        return $this->_productCollection =  $collection;
    }
    
    
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }
    
  /*  public function getManufacturerName(){
        $manufacturer  = Mage::getModel('automativesearch/automativesearch')->getManufacturerName($this->_manufacturer); 
        $this->_manufactuerName = $manufacturer[0]['car_manufacturer_name'];
        return $this->_manufactuerName;
    }
    
    public function getYearName(){
        $year  = Mage::getModel('automativesearch/automativesearch')->getYearName($this->_year); 
        $this->_yearName = $year[0]['car_manufacturer_year_value'];
        return $this->_yearName;
    }
    
    public function getModelName(){
        $model  = Mage::getModel('automativesearch/automativesearch')->getModelName($this->_model); 
        $this->_modelName = $model[0]['car_manufacturer_model_name'];
        return $this->_modelName;    
    }
    
    public function getTypeName(){
        if($this->_type){
            $type  = Mage::getModel('automativesearch/automativesearch')->getTypeName($this->_type); 
            $this->_typeName = $type[0]['car_manufacturer_type_name'];
        }
        else{
            $this->_typeName = "";    
        }
        return $this->_typeName;    
    }*/   
}