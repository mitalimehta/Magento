<?php
  
  
class Ebulb_Purchase_Block_Adminhtml_Inventorycost extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_inventorycost';
        $this->_blockGroup = 'purchase';
        $this->_headerText = Mage::helper('purchase')->__('Inventory Cost');

        parent::__construct();
        
        $this->_removeButton('add');
    }

}   