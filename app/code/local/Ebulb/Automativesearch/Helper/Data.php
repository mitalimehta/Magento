<?php

class Ebulb_Automativesearch_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected  $_name;
    
    public function getAutomativeSearchUrl()
    {
        return $this->_getUrl('automativesearch/search');
    }
    
     public function getAutomativeCategorySearchUrl($category='',$level=0)
    {
        if($level == 1){
            $AutomotiveCategoryURL=$this->_getUrl('automativesearch/manufacturer').strtolower(str_replace(" ","",trim($category))).".html";
            //$AutomotiveCategoryURL=$this->_getUrl('automativesearch/manufacturer')."index/letter/".str_replace(" ","",trim($category));
        }
        else
            $AutomotiveCategoryURL=$this->_getUrl('automativesearch/search')."index/manufacturername/".str_replace(" ","-",$category);           
        return $AutomotiveCategoryURL;
    }
    
     public function changeNameUrl($name){
        $this->_name = strtolower(str_replace(" ","-",$name)); 
        $this->_name = str_replace("&","-and-",$this->_name); 
        $this->_name = str_replace("/","-or-",$this->_name); 
        $this->_name = eregi_replace("[^a-zA-Z0-9\_\-]","",$this->_name);
    
        return $this->_name;        
    }
    
    public function getRedirectUrl($name,$type=''){
        $redirecturl = ''; 
        $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$this->changeNameUrl($name).".html";
                        
        if($type == 'manufacturer')   
            $redirecturl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."automativesearch/search/index/manufacturer/".$this->changeNameUrl($name)."/";                
            
        return $redirecturl;   
    } 
}