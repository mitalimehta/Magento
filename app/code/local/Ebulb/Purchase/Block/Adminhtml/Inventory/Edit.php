<?php

class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {  
        $this->_objectId = 'id';
        $this->_controller = 'inventory';
       
        parent::__construct();
        
        $this->_blockGroup = 'purchase';
        $this->_controller = 'adminhtml_inventory';

        $this->_removeButton('save', 'label', Mage::helper('customer')->__('Save Vendor'));
        $this->_removeButton('delete', 'label', Mage::helper('customer')->__('Delete Vendor'));
       
    }

    public function getHeaderText()
    {
        if( Mage::registry('current_product') && Mage::registry('current_product')->getId() ) {
            return Mage::helper('purchase')->__("Review Item '%s'", $this->htmlEscape(Mage::registry('current_product')->getData('name')));
        } else {
            return Mage::helper('purchase')->__('Add Vendor');
        }
    }
}
