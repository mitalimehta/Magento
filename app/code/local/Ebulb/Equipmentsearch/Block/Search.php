<?php

class Ebulb_Equipmentsearch_Block_Search extends Mage_Catalog_Block_Product_List{ 
     
    protected $_devicetypelist = null;
   
    protected $_productCollection;   
    
    protected $_storeid; 
    
    protected $_deviceId;
    protected $_deviceName;
    protected $_manufId;
    protected $_manufName;
    protected $_equipmentId;  
    
    protected $_manuflist;  
    protected $_equipmentlist;
    
    protected $_name;  
    
    public function _construct()
    {
            
        $this->_deviceId = $this->getRequest()->getParam('deviceid');      
        $this->_manufId = $this->getRequest()->getParam('manufid');      
        $this->_equipmentId = $this->getRequest()->getParam('equipmentid');      

        if($this->_deviceId){ 
            $this->_deviceName =  Mage::getModel('equipmentsearch/equipmentsearch')->getdevicename($this->_deviceId); 
            $this->_manuflist = Mage::getModel('equipmentsearch/equipmentsearch')->getManufCollection($this->_deviceId);     
        }
        
        if($this->_manufId){
            $this->_manufName =  Mage::getModel('equipmentsearch/equipmentsearch')->getmanufname($this->_manufId);  
            $this->_equipmentlist = Mage::getModel('equipmentsearch/equipmentsearch')->getEquipmentCollection($this->_deviceId,$this->_manufId);     
        }
    }
 
    public function getAllDeviceTypes(){
        return $this->_getAllDeviceTypes();  
    }    
    
    public function _getAllDeviceTypes(){
        
        if (is_null($this->_devicetypelist)) {   
            $this->_devicetypelist = Mage::getModel('equipmentsearch/equipmentsearch')->getDeviceTypeCollection();
        }

        return $this->_devicetypelist;
    }
    
    public function getAction()
    {
        return Mage::getUrl('equipmentsearch/search/searchresult');
    }
    
    public function _getProductCollection()
    {   
        //echo $this->_equipmentId;
        if($this->_equipmentId)
        { 
            $this->_storeid = Mage::app()->getStore()->getId(); 
            $this->_productlist   = Mage::getModel('equipmentsearch/equipmentsearch')
                                          ->getProductCollection($this->_equipmentId);
            $productarray = array();
            
            foreach($this->_productlist as $key=>$val){  
                   $productarray[] = $val['product_id']; 
            }
           
           $currentPage = (int) $this->getRequest()->getParam('p', 1);
           $orderby     = $this->getRequest()->getParam('order');
           $dir         = $this->getRequest()->getParam('dir'); 
           $pageSize = (int) $this->getRequest()->getParam('limit',  Mage::getStoreConfig('catalog/frontend/list_per_page'));   
           
           $collection = Mage::getSingleton('catalog/product')->getCollection()
                             ->addAttributeToFilter("entity_id",array("in"=>$productarray))
                             ->addAttributeToSelect('*') 
                             ->addAttributeToFilter("status",array("in"=>1))
                             ->addAttributeToSort($orderby,$dir)
                             //->addAttributeToFilter("visibility",array("in"=>array(2,3,4)))
                             
                             /*
                             ->setVisibility(2) 
                             ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes()) 
                             ->addMinimalPrice()
                             ->addFinalPrice()
                             ->addTaxPercents()
                             ->addStoreFilter()
                             ->addUrlRewrite() 
                             ->addPriceData()
                             
                             */
                             
                             ->addStoreFilter() 
                             ->setPage($currentPage, $pageSize)   
                             ->load();
            //print_r($this->_joinFields);exit;                
            //print_r(get_class_methods($collection));
            //echo $collection->getSelectSql();
            
            //exit;
            
            /*if($orderby == 'position'){
                $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes()) 
                             ->addOptionsToResult();
            } */
           
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);  
            return $this->_productCollection =  $collection;
        }
    }
    
    
    public function getLoadedProductCollection()
    {
        if($this->_equipmentId)
        {
            return $this->_getProductCollection();
        }
    }
    
    
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    } 
    
    protected function _beforeToHtml()
    {   
        if($this->_equipmentId)
        {
            $toolbar = $this->getToolbarBlock();
            
            // called prepare sortable parameters
            $collection = $this->_getProductCollection();
            
            // use sortable parameters
            if ($orders = $this->getAvailableOrders()) {
                $toolbar->setAvailableOrders($orders);
            }
            if ($sort = $this->getSortBy()) {
                $toolbar->setDefaultOrder($sort);
            }
            if ($modes = $this->getModes()) {
                $toolbar->setModes($modes);
            }

            // set collection to tollbar and apply sort
            $toolbar->setCollection($collection);
            
            $this->setChild('toolbar', $toolbar);
            Mage::dispatchEvent('catalog_block_product_list_collection', array(
                'collection'=>$this->_getProductCollection(),
            ));

            $this->_getProductCollection()->load();
            Mage::getModel('review/review')->appendSummary($this->_getProductCollection());
            return parent::_beforeToHtml();
        }
    }
   
    public function getRedirectUrl($type,$name){
        $redirecturl = '';
        
        switch ($type){
            case 'devices':
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$this->changeNameUrl($name).".html";                
                break;
            case 'manuf':        
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$this->changeNameUrl($this->_deviceName)."-".$this->changeNameUrl($name).".html";                
                break;
            case 'equipments':        
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$this->changeNameUrl($this->_deviceName)."-".$this->changeNameUrl($this->_manufName)."-".$this->changeNameUrl($name).".html";                
                break;
            default :
                $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        }
        return $redirecturl;   
    }
    
    public function changeNameUrl($name){
        $this->_name = strtolower(str_replace(" ","-",$name)); 
        $this->_name = str_replace("&","-and-",$this->_name); 
        $this->_name = str_replace("/","-or-",$this->_name); 
        $this->_name = eregi_replace("[^a-zA-Z0-9\_\-]","",$this->_name);
        $this->_name = eregi_replace("[^a-zA-Z0-9\_\-]","",$this->_name);
        $this->_name = eregi_replace("--","-",$this->_name);                      
        $this->_name = eregi_replace("-\.",".",$this->_name); 
        return $this->_name;        
    }
}