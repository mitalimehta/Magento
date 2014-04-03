<?php

class Ebulb_Automativesearch_Block_Searchresult extends Mage_Catalog_Block_Product_List{   
     
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
    
    protected  $_Manufacturerid;
    protected  $_Manufacturername;
    
    protected  $_Yearid;
    protected  $_Modelid;
    protected  $_Typeid;
    protected  $_Locationid;
   
    public function __construct(){
    }
   
    public function _getProductCollection()
    {  

        if(Mage::app()->getRequest()->getParam('manufid')) {
            $this->_manufacturer  = Mage::helper('cms/utils')->encodeSpecialChars( Mage::app()->getRequest()->getParam('manufid')); 
            Mage::getSingleton('core/session')->setcarmanufacturerid($this->_manufacturer);    
        }
        else{
            $this->_manufacturer = Mage::getSingleton('core/session')->getcarmanufacturerid();    
        }
         
        if(Mage::app()->getRequest()->getParam('yearid')) {  
            $this->_year          = Mage::helper('cms/utils')->encodeSpecialChars( Mage::app()->getRequest()->getParam('yearid')); 
            Mage::getSingleton('core/session')->setcarmanufactureryearid($this->_year);          
        }
        else{
            $this->_year = Mage::getSingleton('core/session')->getcarmanufactureryearid();      
        }
         
        if(Mage::app()->getRequest()->getParam('modelid')){
            $this->_model         = Mage::helper('cms/utils')->encodeSpecialChars( Mage::app()->getRequest()->getParam('modelid'));    
            Mage::getSingleton('core/session')->setcarmanufacturermodelid($this->_model);     
        }
        else{
            $this->_model = Mage::getSingleton('core/session')->getcarmanufacturermodelid();      
        }
         
        if(Mage::app()->getRequest()->getParam('typeid')){
            $this->_type         = Mage::helper('cms/utils')->encodeSpecialChars( Mage::app()->getRequest()->getParam('typeid'));    
            Mage::getSingleton('core/session')->setcarmanufacturertypeid($this->_type);     
        }
        else{
            $this->_type = Mage::getSingleton('core/session')->getcarmanufacturertypeid();      
        }  
         
        if(Mage::app()->getRequest()->getParam('locid')){
            $this->_location         = Mage::helper('cms/utils')->encodeSpecialChars( Mage::app()->getRequest()->getParam('locid'));    
            Mage::getSingleton('core/session')->setcarmanufacturerlocationid($this->_location);     
        }
        else{
            $this->_location = Mage::getSingleton('core/session')->getcarmanufacturerlocationid();      
        }
        
       $this->_Manufacturername     = Mage::helper('cms/utils')->encodeSpecialChars( $this->getRequest()->getParam('manufacturer')); 
       $this->_yearName             = Mage::helper('cms/utils')->encodeSpecialChars( $this->getRequest()->getParam('year')); 
       $this->_modelName            = Mage::helper('cms/utils')->encodeSpecialChars( $this->getRequest()->getParam('model')); 
       $this->_typeName             = Mage::helper('cms/utils')->encodeSpecialChars( $this->getRequest()->getParam('type')); 
       $this->_locationName         = Mage::helper('cms/utils')->encodeSpecialChars( $this->getRequest()->getParam('location')); 
       
       $this->_manufacturerslist    = $this->getAllManufacturers();
       foreach($this->_manufacturerslist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_Manufacturername) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Manufacturerid = $_value['option_id'];    
                $this->_Manufacturer  = $_value['value'];    
           }
       } 
     
       if($this->_Manufacturerid){
           
            $this->_yearlist = Mage::getModel('automativesearch/automativesearch')->getYearCollection($this->_Manufacturerid); 
       }
       
       foreach($this->_yearlist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_yearName) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Yearid = $_value['option_id'];    
                $this->_Year = $_value['value'];    
           }
       }
       
       if($this->_Manufacturerid && $this->_Yearid){
            $this->_modellist = Mage::getModel('automativesearch/automativesearch')->getModelCollection($this->_Manufacturerid,$this->_Yearid); 
       }
       
       foreach($this->_modellist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_modelName) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Modelid = $_value['option_id'];    
                $this->_model = $_value['value'];    
           }
       }
       
       if ($this->_Manufacturerid =='' || $this->_Yearid == '' || $this->_Modelid ==''){
            return;
       }      
       
       if($this->_Manufacturerid && $this->_Yearid && $this->_Modelid){
            $this->_typelist = Mage::getModel('automativesearch/automativesearch')->getTypeCollection($this->_Manufacturerid,$this->_Yearid,$this->_Modelid); 
       } 
      
       foreach($this->_typelist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_typeName) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Typeid = $_value['option_id'];    
                $this->_type = $_value['value'];    
           }
       }
       
       $this->_locationlist = Mage::getModel('automativesearch/automativesearch')->getlocationCollection($this->_Manufacturerid,$this->_Yearid,$this->_Modelid,$this->_Typeid);
        
       foreach($this->_locationlist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_locationName) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Locationid = $_value['option_id'];    
           }
       }  
    
       $this->_productlist   = Mage::getModel('automativesearch/automativesearch')
                                      ->getProductCollection($this->_Manufacturerid,$this->_Yearid,$this->_Modelid,$this->_Typeid,$this->_Locationid);
       
       
        $productarray = array();
        if(count($this->_productlist) > 0){
            foreach($this->_productlist as $key=>$val){  
               $productarray[] = $val['product_id']; 
            } 
        }
        
       $currentPage = (int) $this->getRequest()->getParam('p', 1);
           
       $orderby = $this->getRequest()->getParam('order');
       $dir     = $this->getRequest()->getParam('dir'); 
         
       $pageSize = (int) $this->getRequest()->getParam('limit',  Mage::getStoreConfig('catalog/frontend/list_per_page'));
       
        $collection = Mage::getSingleton('catalog/product')->getCollection()
                         ->addAttributeToFilter("entity_id",array("in"=>$productarray))
                         ->addAttributeToSelect('*')
                         ->addStoreFilter()
                         ->addAttributeToFilter("status",array("in"=>1))
                         ->addAttributeToFilter("visibility",array("in"=>array(2,3,4))) 
                         ->setPage($currentPage, $pageSize)
                         ->load();  
       
      
        return $this->_productCollection =  $collection;
    }
     
    public function getAllManufacturers(){
       
        return $this->_getAllManufacturers();  
    }    
    
    public function _getAllManufacturers(){
        
        if (is_null($this->_manufacturerslist)) {   
            $this->_manufacturerslist = Mage::getModel('automativesearch/automativesearch')->getManufacturerCollection();
        }

        return $this->_manufacturerslist;
    }  
    
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }
    
    public function getManufacturerName(){
        //$manufacturer  = Mage::getModel('automativesearch/automativesearch')->getManufacturerName($this->_manufacturer); 
        //$this->_manufactuerName = $manufacturer[0]['car_manufacturer_name'];
        return $this->_Manufacturer;
    }
    
    public function getYearName(){
        //$year  = Mage::getModel('automativesearch/automativesearch')->getYearName($this->_year); 
        //$this->_yearName = $year[0]['car_manufacturer_year_value'];
       // echo $this->_Year;exit;
        return $this->_Year;
    }
    
    public function getModelName(){
        //$model  = Mage::getModel('automativesearch/automativesearch')->getModelName($this->_model); 
        //$this->_modelName = $model[0]['car_manufacturer_model_name'];
        return $this->_model;    
    }
    
    public function getTypeName(){
        /*if($this->_type){
            $type  = Mage::getModel('automativesearch/automativesearch')->getTypeName($this->_type); 
            $this->_typeName = $type[0]['car_manufacturer_type_name'];
        }
        else{
            $this->_typeName = "";    
        } */
        return $this->_type;    
    }
    
    public function getSearchBaseURL(){
        $storeurl =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK); 
        $storeBaseurl = $storeurl."automativesearch/search/index/";
        return $storeBaseurl;
    }
    
    public function getManufacturerUrl(){
        return $this->getSearchBaseURL().'manufid/'.$this->_manufacturer;
    }
    
    public function getYearUrl(){
        return $this->getSearchBaseURL().'manufid/'.$this->_manufacturer.'/yearid/'.$this->_year; 
    }
    
    public function getModelUrl(){
        return $this->getSearchBaseURL().'manufid/'.$this->_manufacturer.'/yearid/'.$this->_year.'/modelid/'.$this->_model; 
    }
    
    public function getTypeUrl(){
        return $this->getSearchBaseURL().'manufid/'.$this->_manufacturer.'/yearid/'.$this->_year.'/modelid/'.$this->_model.'/typeid/'.$this->_type; 
    }
    
    
    public function getRedirectUrl($type,$name){
        $redirecturl = '';
        
        switch ($type){
            case 'manufacturers':
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."automativesearch/search/index/manufacturer/".$this->helper('automativesearch')->changeNameUrl($name)."/";                
                break;
            case 'years':        
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."automativesearch/search/index/manufacturer/".$this->helper('automativesearch')->changeNameUrl($this->_Manufacturername)."/year/".$this->helper('automativesearch')->changeNameUrl($name)."/";                
                break;
            case 'models':        
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."automativesearch/search/index/manufacturer/".$this->helper('automativesearch')->changeNameUrl($this->_Manufacturername)."/year/".$this->helper('automativesearch')->changeNameUrl($this->_yearName)."/model/".$this->helper('automativesearch')->changeNameUrl($name)."/";                
                break;
            case 'types':        
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."automativesearch/search/index/manufacturer/".$this->helper('automativesearch')->changeNameUrl($this->_Manufacturername)."/year/".$this->helper('automativesearch')->changeNameUrl($this->_yearName)."/model/".$this->helper('automativesearch')->changeNameUrl($this->_modelName)."/type/".$this->helper('automativesearch')->changeNameUrl($name)."/";                
                break;
            default :
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        }
        return $redirecturl;   
    }
}