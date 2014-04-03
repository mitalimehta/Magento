<?php
  
  
class Ebulb_Automativesearch_Block_Adminhtml_Automotivebulbs extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_Automotivebulbs';
        $this->_blockGroup = 'automativesearch';
        $this->_headerText = Mage::helper('automativesearch')->__('Manage Automotive Bulbs');
        $this->_addButtonLabel = Mage::helper('automativesearch')->__('Add New');
        parent::__construct();
    }

} 
  
?>