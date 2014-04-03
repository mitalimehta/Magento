<?php
  
  
class Ebulb_Automaticfeed_Block_Adminhtml_Company extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_company';
        $this->_blockGroup = 'automaticfeed';
        $this->_headerText = Mage::helper('purchase')->__('Manage Company');
        $this->_addButtonLabel = Mage::helper('purchase')->__('Add New Company');
        parent::__construct();
    }

} 