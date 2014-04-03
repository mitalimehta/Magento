<?php
  
  
class Ebulb_Purchase_Block_Adminhtml_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_vendor';
        $this->_blockGroup = 'purchase';
        $this->_headerText = Mage::helper('purchase')->__('Manage Vendors');
        $this->_addButtonLabel = Mage::helper('purchase')->__('Add New Vendor');
        parent::__construct();
    }

}   