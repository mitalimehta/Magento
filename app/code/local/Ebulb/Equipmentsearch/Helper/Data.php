<?php

class Ebulb_Equipmentsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEquipmentSearchUrl()
    {
        return $this->_getUrl('equipmentsearch/search');
    }
    
     /*public function getAutomativeCategorySearchUrl($category='',$level=0)
    {
        if($level == 1)
            $AutomotiveCategoryURL=$this->_getUrl('automativesearch/manufacturer')."index/letter/".str_replace(" ","",trim($category));
        else
            $AutomotiveCategoryURL=$this->_getUrl('automativesearch/search')."index/manufacturername/".str_replace(" ","-",$category);
        return $AutomotiveCategoryURL;
    }*/
}