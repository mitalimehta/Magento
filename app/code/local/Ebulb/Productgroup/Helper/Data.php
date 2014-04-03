<?php

class Ebulb_Productgroup_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function combineItems($arr)
  {
     if(!$arr)return array();
     
     $shifted  = array_shift($arr); 
    
     $combined = $this->combineItems($arr);
     $result  = array();
     foreach($shifted as $v){
             if(!$combined) return $shifted; 
             foreach($combined as $z){   
                     if(is_array($z)) 
                        $z[] = $v;
                     else   
                        $z   = array($z,$v);
                     $result[] = $z;
             }
     }
     return $result;        
  }
}