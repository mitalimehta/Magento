<?php
  
  
class Ebulb_Customsearch_Block_Adminhtml_Searchattribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_Searchattribute';
        $this->_blockGroup = 'customsearch';
        $this->_headerText = Mage::helper('customsearch')->__('Manage Search Attributes');
        $this->_addButtonLabel = Mage::helper('customsearch')->__('Add New Search');
        parent::__construct();
    }

} 
  
?>