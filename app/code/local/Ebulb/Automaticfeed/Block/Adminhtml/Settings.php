<?php
  
  
class Ebulb_Automaticfeed_Block_Adminhtml_Settings extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_settings';
        $this->_blockGroup = 'automaticfeed';
        $this->_headerText = Mage::helper('automaticfeed')->__('Manage Settings');
        $this->_addButtonLabel = Mage::helper('automaticfeed')->__('Add New Settings');
        parent::__construct();
    }

} 