<?php

class Ebulb_Automativesearch_Block_Search extends Mage_Core_Block_Template{ 
     
    protected $_manufacturerslist = null;
    
    protected  $_ManufacturerSelect;
    protected  $_ManufacturerSelectid;
    
    protected  $_Manufacturerid;
    protected  $_Manufacturername;
    
    protected  $_Yearid;
    protected  $_Modelid;
    protected  $_Typeid;
    protected  $_Locationid;
    
    protected $_yearlist = array();  
    protected $_modellist = array();
    protected $_typelist = array();
    protected $_productlist = array();
    
    protected $_locationlist = array(); 
    
    protected $_manufactuerName;
    protected $_yearName;
    protected $_modelName;
    protected $_typeName; 
    
    protected $_imagetag;
     
    public function _construct()
    {
        Mage::getSingleton('core/session')->setcarmanufacturerid(null); 
        Mage::getSingleton('core/session')->setcarmanufactureryearid(null); 
        Mage::getSingleton('core/session')->setcarmanufacturermodelid(null); 
        Mage::getSingleton('core/session')->setcarmanufacturertypeid(null); 
        Mage::getSingleton('core/session')->setcarmanufacturerlocationid(null);
        
       $this->_ManufacturerSelect = $this->getRequest()->getParam('manufacturername'); 
       
       $this->_Manufacturername     = $this->getRequest()->getParam('manufacturer'); 
       $this->_yearName             = $this->getRequest()->getParam('year'); 
       $this->_modelName            = $this->getRequest()->getParam('model'); 
       $this->_typeName             = $this->getRequest()->getParam('type'); 
      
       //$this->_Modelid              = $this->getRequest()->getParam('modelid'); 
       //$this->_Typeid               = $this->getRequest()->getParam('typeid'); 
       $this->_Locationid           = $this->getRequest()->getParam('locid'); 
       
       $this->_manufacturerslist  = $this->getAllManufacturers();
       foreach($this->_manufacturerslist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_Manufacturername) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Manufacturerid = $_value['option_id'];    
           }
       }
       
/*     
        if($this->_ManufacturerSelect){
            $this->_Manufacturerid = Mage::getModel('automativesearch/automativesearch')->getManufacturerIDFromName($this->_ManufacturerSelect); 
            
       } 
*/            
       if($this->_Manufacturerid){
           
            $this->_yearlist = Mage::getModel('automativesearch/automativesearch')->getYearCollection($this->_Manufacturerid); 
       }
       
       foreach($this->_yearlist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_yearName) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Yearid = $_value['option_id'];    
           }
       }
       
       if($this->_Manufacturerid && $this->_Yearid){
            $this->_modellist = Mage::getModel('automativesearch/automativesearch')->getModelCollection($this->_Manufacturerid,$this->_Yearid); 
       }
       
       foreach($this->_modellist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_modelName) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Modelid = $_value['option_id'];    
           }
       }
      
       
       if($this->_Manufacturerid && $this->_Yearid && $this->_Modelid){
            $this->_typelist = Mage::getModel('automativesearch/automativesearch')->getTypeCollection($this->_Manufacturerid,$this->_Yearid,$this->_Modelid); 
       } 
      
       foreach($this->_typelist as $_key=>$_value){
           if ($this->helper('automativesearch')->changeNameUrl($this->_typeName) == $this->helper('automativesearch')->changeNameUrl($_value['value'])){
                $this->_Typeid = $_value['option_id'];    
           }
       }
      
       if(($this->_Manufacturerid && $this->_Yearid && $this->_Modelid && $this->_Typeid) || ($this->_Manufacturerid && $this->_Yearid && $this->_Modelid && count($this->_typelist) == 0)){    
        
            $this->_locationlist = Mage::getModel('automativesearch/automativesearch')->getlocationCollection($this->_Manufacturerid,$this->_Yearid,$this->_Modelid,$this->_Typeid);
            
            
            
            /*if($this->_Typeid){
                $type  = Mage::getModel('automativesearch/automativesearch')->getTypeName($this->_Typeid); 
                $this->_typeName = $type[0]['car_manufacturer_type_name'];
            }
            else{
                $this->_typeName = "";    
            } */
            
            //echo $this->_typeName;exit;
            $CarImageNamewithtype = $this->_yearName."-".str_replace(" ","_",strtolower(trim($this->_Manufacturername)))."-".str_replace(" ","_",strtolower(trim($this->_modelName)))."-".str_replace(" ","_",strtolower(trim($this->_typeName))).".jpg";
   
            $CarImageNamewithouttype = $this->_yearName."-".str_replace(" ","_",strtolower(trim($this->_Manufacturername)))."-".str_replace(" ","_",strtolower(trim($this->_modelName))).".jpg";
            
            $CarImageNamewithoutmodels =  $this->_yearName."-".str_replace(" ","_",strtolower(trim($this->_Manufacturername))).".jpg"; 
            
            $absolute_path = Mage::getBaseDir('media')."/AutomativeSearch/";
            $CarImageURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."AutomativeSearch/";
             
            $this->_imagetag = '';
            if(file_exists($absolute_path.$CarImageNamewithtype))
                $this->_imagetag = "<img src='".$CarImageURL.$CarImageNamewithtype."' alt='' />" ;      
            else if(file_exists($absolute_path.$CarImageNamewithouttype))
                $this->_imagetag = "<img src='".$CarImageURL.$CarImageNamewithouttype."' alt='' />" ;      
            else if(file_exists($absolute_path.$CarImageNamewithoutmodels))
                $this->_imagetag = "<img src='".$CarImageURL.$CarImageNamewithoutmodels."' alt='' />" ;      
            else
                $this->_imagetag = "<img src='".$CarImageURL.'coming-soon1.gif'."' alt='' />" ; 
                  
       }
        
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
    
    public function getAction()
    {
        return Mage::getUrl('automativesearch/search/searchresult');
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
            case 'productlist':
                if($this->_typeName){
                    $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."automativesearch/search/searchresult/manufacturer/".$this->helper('automativesearch')->changeNameUrl($this->_Manufacturername)."/year/".$this->helper('automativesearch')->changeNameUrl($this->_yearName)."/model/".$this->helper('automativesearch')->changeNameUrl($this->_modelName)."/type/".$this->helper('automativesearch')->changeNameUrl($this->_typeName)."/location/".$this->helper('automativesearch')->changeNameUrl($name)."/";                
                }
                else{
                   // $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$this->helper('automativesearch')->changeNameUrl($this->_Manufacturername)."-".$this->helper('automativesearch')->changeNameUrl($this->_yearName)."-".$this->helper('automativesearch')->changeNameUrl($this->_modelName)."-".$this->helper('automativesearch')->changeNameUrl($name).".html";                
                    $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."automativesearch/search/searchresult/manufacturer/".$this->helper('automativesearch')->changeNameUrl($this->_Manufacturername)."/year/".$this->helper('automativesearch')->changeNameUrl($this->_yearName)."/model/".$this->helper('automativesearch')->changeNameUrl($this->_modelName)."/location/".$this->helper('automativesearch')->changeNameUrl($name)."/";                
                }
                break;
            default :
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        }
        return $redirecturl;   
    }
    
   
}