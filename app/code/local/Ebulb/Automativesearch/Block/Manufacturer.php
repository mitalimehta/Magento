<?php

class Ebulb_Automativesearch_Block_Manufacturer extends Mage_Core_Block_Template{ 
     
    protected $_letter;   
    protected $_manufacturerList ;
   
    public function _construct()
    {
         
    }
 
    public function _getManufacturerByLetter(){
        $this->_letter = $this->getRequest()->getParam('letter'); 
        $this->_manufacturerList = Mage::getModel('automativesearch/automativesearch')->getManufacturersByLetter($this->_letter); 
        return $this->_manufacturerList;
    }
 
    public function getManufacturerByLetter(){                   
        return $this->_getManufacturerByLetter();                
    }
 
}