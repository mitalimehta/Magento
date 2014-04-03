<?php
  
  
class Ebulb_Purchase_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_inventory';
        $this->_blockGroup = 'purchase';
        $this->_headerText = Mage::helper('purchase')->__('Manage Inventory');
        
        parent::__construct();
        
        $this->_removeButton('add');
    }

}   