<?php

class Ebulb_Automativesearch_SearchController extends Mage_Core_Controller_Front_Action
{
    protected $_manufacturer;
    protected $_year;
    protected $_model;
    protected $_type;  
    protected $_location;  
    
    protected $_yearlist;  
    protected $_modellist;
    protected $_typelist;
    protected $_productlist;
    
    protected $_locationlist;
    
    protected $_productCollection;
    
    protected $_manufactuerName;
    protected $_yearName;
    protected $_modelName;
    protected $_typeName; 
    
   
    
    public function indexAction()
    { 
        $this->loadLayout();     
        $this->renderLayout();  
        
    }
    
    public function getdataAction(){
        
        $this->_manufacturer  = $this->getRequest()->getParam('manufacturer');
        $this->_year          = $this->getRequest()->getParam('year');
        $this->_model         = $this->getRequest()->getParam('model');
        $this->_type          = $this->getRequest()->getParam('type');
        
        
        if(($this->_year == '' || $this->_year == NULL) && ($this->_model == '' || $this->_model == NULL) && ($this->_type == '' || $this->_type == NULL)){
            $this->_yearlist = Mage::getModel('automativesearch/automativesearch')->getYearCollection($this->_manufacturer);
            
            $yearliststring = '';
            foreach($this->_yearlist as $key=>$val){
                $yearliststring.= $val['car_manufacturer_year_id']."####@@@@####".$val['car_manufacturer_year_value']."####@@@@####";
            }
            
            echo "years_selectbox####~~~~####".$yearliststring;
        }
        else if(($this->_model == '' || $this->_model == NULL) && ($this->_type == '' || $this->_type == NULL)){ 
            $this->_modellist = Mage::getModel('automativesearch/automativesearch')->getModelCollection($this->_manufacturer,$this->_year);         
            
            $modelliststring = ''; 
            foreach($this->_modellist as $key=>$val){
                $modelliststring.= $val['car_manufacturer_model_id']."####@@@@####".$val['car_manufacturer_model_name']."####@@@@####";
            }
            
            echo "models_selectbox####~~~~####".$modelliststring;
        }
        else if($this->_type == '' || $this->_type == NULL){
            $this->_typelist = Mage::getModel('automativesearch/automativesearch')->getTypeCollection($this->_manufacturer,$this->_year,$this->_model);             
            
            $typeliststring = '';
            foreach($this->_typelist as $key=>$val){
                $typeliststring.= $val['car_manufacturer_type_id']."####@@@@####".$val['car_manufacturer_type_name']."####@@@@####";
            }
            
            echo "types_selectbox####~~~~####".$typeliststring;
        }
        return;
    }
    
    public function getcarlocationsAction(){
        
        $this->_manufacturer  = $this->getRequest()->getParam('manufacturer');
        $this->_year          = $this->getRequest()->getParam('year');
        $this->_model         = $this->getRequest()->getParam('model');
        $this->_type          = $this->getRequest()->getParam('type');
        
        $this->_locationlist = Mage::getModel('automativesearch/automativesearch')->getlocationCollection($this->_manufacturer,$this->_year,$this->_model,$this->_type);
   
        $manufacturer  = Mage::getModel('automativesearch/automativesearch')->getManufacturerName($this->_manufacturer); 
        $this->_manufactuerName = $manufacturer[0]['car_manufacturer_name']; 
        
        $year  = Mage::getModel('automativesearch/automativesearch')->getYearName($this->_year); 
        $this->_yearName = $year[0]['car_manufacturer_year_value'];
        
        $model  = Mage::getModel('automativesearch/automativesearch')->getModelName($this->_model); 
        $this->_modelName = $model[0]['car_manufacturer_model_name'];
        
        if($this->_type){
            $type  = Mage::getModel('automativesearch/automativesearch')->getTypeName($this->_type); 
            $this->_typeName = $type[0]['car_manufacturer_type_name'];
        }
        else{
            $this->_typeName = "";    
        }
            
        $CarImageNamewithtype = $this->_yearName."-".str_replace(" ","_",strtolower(trim($this->_manufactuerName)))."-".str_replace(" ","_",strtolower(trim($this->_modelName)))."-".str_replace(" ","_",strtolower(trim($this->_typeName))).".jpg";
   
        $CarImageNamewithouttype = $this->_yearName."-".str_replace(" ","_",strtolower(trim($this->_manufactuerName)))."-".str_replace(" ","_",strtolower(trim($this->_modelName))).".jpg";
        
        $CarImageNamewithoutmodels =  $this->_yearName."-".str_replace(" ","_",strtolower(trim($this->_manufactuerName))).".jpg"; 
        
        $absolute_path = Mage::getBaseDir('media')."/AutomativeSearch/";
        $CarImageURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."AutomativeSearch/";
         
        $imagetag = '';
        if(file_exists($absolute_path.$CarImageNamewithtype))
            $imagetag = "<img src='".$CarImageURL.$CarImageNamewithtype."' alt='' />" ;      
        else if(file_exists($absolute_path.$CarImageNamewithouttype))
            $imagetag = "<img src='".$CarImageURL.$CarImageNamewithouttype."' alt='' />" ;      
        else if(file_exists($absolute_path.$CarImageNamewithoutmodels))
            $imagetag = "<img src='".$CarImageURL.$CarImageNamewithoutmodels."' alt='' />" ;      
        else
            $imagetag = "<img src='".$CarImageURL.'coming-soon1.gif'."' alt='' />" ;    
   
        if($imagetag != ''){
            echo "<div class='page-title category-title'><h1>".$this->_manufactuerName." ".$this->_yearName." ".$this->_modelName." ".$this->_typeName."</h1></div>";
            echo $imagetag;
        }
   
        $locationstring = "";
        $totalrecords = count($this->_locationlist);
        
        echo "<table cellpadding='2' cellspacing='2' width='100%'>";
        echo "<tr><td colspan='3' align='left'><span class='lighting-location'><br />Select Lighting Location</span><br /><br /></td></tr>";
        
        foreach($this->_locationlist as $key=>$val){
            
            if($key % 3  === 0)
                echo "<tr>";
                
            echo "<td width='33%' align='left'>";
            echo "<a href='#' onclick='getSearchResults(".$val['location_id'].")' class='lighting-location-a'>". $val['location_name']."</a>";        
            //echo "<a href='#' onclick='document.searchbymanufacturer.submit();'>". $val['location_name']."</a>";        
            echo "</td>";
            
            if($key % 3  === 0 && $key % 3 !==0)   
                echo "</tr>";    
                
        }
        echo "</table>";
       
        echo $locationstring;
        return; 
        
    }
    
    public function searchresultAction(){
         
        $this->loadLayout();   
        $this->renderLayout();    
        
    }
    
    public function homepageAction(){
          
    }
}
  

