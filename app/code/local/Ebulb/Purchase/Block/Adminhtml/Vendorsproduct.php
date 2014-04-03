<?php
  
  
class Ebulb_Purchase_Block_Adminhtml_Vendorsproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_vendorsproduct';
        $this->_blockGroup = 'purchase';
        $this->_headerText = Mage::helper('purchase')->__('Vendor\'s Product');
        
        parent::__construct();
        
        $this->_removeButton('add');
    }

}   