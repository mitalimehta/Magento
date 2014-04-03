<?php
   
class Ebulb_Productgroup_Model_Productgroup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productgroup/productgroup');
    }
    
    public function savegroupattributes($productattributes,$productattributecode,$groupid){
        return $this->getResource()->savegroupattributes($productattributes,$productattributecode,$groupid); 
    }
    
    public function savegroupattributeoptions($productattributeoptions,$attrid){
        return $this->getResource()->savegroupattributeoptions($productattributeoptions,$attrid); 
    }
    
    public function getattributesbygroupid($groupid){
        return $this->getResource()->getattributesbygroupid($groupid); 
    }
    
    public function getattributeoptionsbygroupid($groupid,$attributeid){
        return $this->getResource()->getattributeoptionsbygroupid($groupid,$attributeid);      
    }   
    
    public function saveproducts(){
        return $this->getResource()->saveproducts();          
    }
    
    public function getproductskubycombination($groupid,$optionstr){
         return $this->getResource()->getproductskubycombination($groupid,$optionstr);     
    }
    
    public function getgroupid($productid){
         return $this->getResource()->getgroupid($productid);               
    }
    
    public function getproductidbycombination($groupid,$optionstr){   
        return $this->getResource()->getproductidbycombination($groupid,$optionstr); 
    }
    
    public function getproductidstorefilter($groupid,$optionstr){   
        return $this->getResource()->getproductidstorefilter($groupid,$optionstr); 
    } 
   
}