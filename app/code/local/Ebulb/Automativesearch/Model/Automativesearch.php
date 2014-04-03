<?php
   
class Ebulb_Automativesearch_Model_Automativesearch extends Mage_Core_Model_Abstract
{      
    public function _construct()
    {
        parent::_construct();
        $this->_init('automativesearch/automativesearch');
    }  
    
    public function getManufacturerCollection(){
        return $this->getResource()->getManufacturerCollection(); 
    }
    
    public function getYearCollection($manufacturerid = ''){
        return $this->getResource()->getYearCollection($manufacturerid); 
    }
    
    public function getModelCollection($manufacturerid='',$yearid=''){
        return $this->getResource()->getModelCollection($manufacturerid,$yearid); 
    }
    
    public function getTypeCollection($manufacturerid='',$yearid='',$modelid=''){
        return $this->getResource()->getTypeCollection($manufacturerid,$yearid,$modelid); 
    }
    
    public function getProductCollection($manufacturerid='',$yearid='',$modelid='',$typeid='',$locationid=''){
        return $this->getResource()->getProductCollection($manufacturerid,$yearid,$modelid,$typeid,$locationid); 
    }
    
    public function getlocationCollection($manufacturerid='',$yearid='',$modelid='',$typeid=''){
        return $this->getResource()->getlocationCollection($manufacturerid,$yearid,$modelid,$typeid);
    }
    
    public function getManufacturerName($manufacturerid){
        return $this->getResource()->getManufacturerName($manufacturerid);         
    }
    
    public function getYearName($yearid){
        return $this->getResource()->getYearName($yearid);         
    }
    
    public function getModelName($modelid){
        return $this->getResource()->getModelName($modelid);         
    }
    
    public function getTypeName($typeid){
        return $this->getResource()->getTypeName($typeid);         
    }
    
    public function getAttrbiuteId($attribute_code){
        return $this->getResource()->getAttrbiuteId($attribute_code);         
    }
    
    public function getAllOptions($attribute_code=''){
        return $this->getResource()->getAllOptions($attribute_code);         
    } 
    
    
    public function getManufacturerIDFromName($manufacturerName){
        if($manufacturerName){
            $manufactureridarr = $this->getResource()->getManufacturerIDFromName($manufacturerName); 
            if($manufactureridarr) {
                return $manufactureridarr[0]['car_manufacturer_id'];       
            }
            else 
                return '';
        }
        else
            return '';
    }
    
    public function getManufacturersByLetter($letter){
        return $this->getResource()->getManufacturersByLetter($letter); 
    }
    
    public function saveautomotivebulbs($automotivebulbs){
        return $this->getResource()->saveautomotivebulbs($automotivebulbs); 
    }
    
    public function getAutomotiveBulbs($car_manufacturer_id,$car_manufacturer_year_id,$car_manufacturer_model_id,$car_manufacturer_type_id,$id=''){
        return $this->getResource()->getAutomotiveBulbs($car_manufacturer_id,$car_manufacturer_year_id,$car_manufacturer_model_id,$car_manufacturer_type_id,$id); 
    }
    
    public function deletecombination(){
        return $this->getResource()->deletecombination($this->getData('car_manufacturer_id'),$this->getData('car_manufacturer_year_id'),$this->getData('car_manufacturer_model_id'),$this->getData('car_manufacturer_type_id'),$this->getId()); 
    }
    
    
}