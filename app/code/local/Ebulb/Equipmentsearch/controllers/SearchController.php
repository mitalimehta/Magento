<?php

class Ebulb_Equipmentsearch_SearchController extends Mage_Core_Controller_Front_Action
{
    protected $_device;    
    protected $_manuf;
    protected $_equipment;
    protected $_type;  
    protected $_location;  
    
    protected $_manuflist;  
    protected $_equipmentlist;
    
    protected $_equipmentData = array();
    
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
        
        $this->_device         = $this->getRequest()->getParam('device');
        $this->_manuf          = $this->getRequest()->getParam('manuf');
        $this->_equipment       = $this->getRequest()->getParam('equipment');
       // $this->_type           = $this->getRequest()->getParam('type');
        
        
        if(($this->_manuf == '' || $this->_manuf == NULL) && ($this->_equipment == '' || $this->_equipment == NULL)){
            $this->_manuflist = Mage::getModel('equipmentsearch/equipmentsearch')->getManufCollection($this->_device);
            
            $manufliststring = '';
            foreach($this->_manuflist as $key=>$val){
                $manufliststring.= $val['manuf_id']."####@@@@####".$val['name']."####@@@@####";
            }
            
            echo "manuf_selectbox####~~~~####".$manufliststring;
        }
        else if(($this->_equipment == '' || $this->_equipment == NULL)){ 
            $this->_equipmentlist = Mage::getModel('equipmentsearch/equipmentsearch')->getEquipmentCollection($this->_device,$this->_manuf);         
            
            $equipmentliststring = ''; 
            foreach($this->_equipmentlist as $key=>$val){
                $equipmentliststring.= $val['equipment_id']."####@@@@####".$val['name']."####@@@@####";
            }
            
            echo "equipment_selectbox####~~~~####".$equipmentliststring;
        }
       
        return;
    }
    
    public function searchresultsAction(){   
        $this->loadLayout();   
        $this->renderLayout();
    }
    
    
}
  

