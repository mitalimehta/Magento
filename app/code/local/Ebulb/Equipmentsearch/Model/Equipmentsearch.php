<?php
   
class Ebulb_Equipmentsearch_Model_Equipmentsearch extends Mage_Core_Model_Abstract
{      
    public function _construct()
    {
        parent::_construct();
        $this->_init('equipmentsearch/equipmentsearch');
    }  
    
    public function getDeviceTypeCollection(){
        return $this->getResource()->getDeviceTypeCollection(); 
    }
    
    public function getManufCollection($deviceid = ''){
        return $this->getResource()->getManufCollection($deviceid); 
    }
    
    public function getEquipmentCollection($deviceid='',$manufid=''){
        return $this->getResource()->getEquipmentCollection($deviceid,$manufid); 
    }
    
    public function getEquipmentDetails($equipment=''){
        return $this->getResource()->getEquipmentDetails($equipment); 
    } 
    
    
    public function getProductCollection($equipment=''){
        return $this->getResource()->getProductCollection($equipment); 
    }
    
    public function getdevicename($deviceid=''){
        return $this->getResource()->getdevicename($deviceid); 
    }
    
    public function getmanufname($manufid=''){   
        return $this->getResource()->getmanufname($manufid); 
    }
   
     
}