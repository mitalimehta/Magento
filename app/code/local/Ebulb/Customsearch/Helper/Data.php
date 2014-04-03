<?php

class Ebulb_Customsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
   public function getProjectorSearchUrl(){
       return $this->_getUrl('customsearch/projectorsearch');
   }
   
   public function getCustomSearchUrl($entity_id = null){
       return $this->_getUrl('customsearch/customsearch/',array("entity_id"=>$entity_id));
   }
   
   
}

?>