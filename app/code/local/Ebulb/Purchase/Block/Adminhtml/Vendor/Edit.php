<?php

class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {  
        $this->_objectId = 'id';
        $this->_controller = 'vendor';
       
        parent::__construct();
        
        $this->_blockGroup = 'purchase';
        $this->_controller = 'adminhtml_vendor';

        $this->_updateButton('save', 'label', Mage::helper('customer')->__('Save Vendor'));
        $this->_updateButton('delete', 'label', Mage::helper('customer')->__('Delete Vendor'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productgroup_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'purchase_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'purchase_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('current_vendor') && Mage::registry('current_vendor')->getId() ) {
            return Mage::helper('purchase')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('current_vendor')->getData('vendor_name')));
        } else {
            return Mage::helper('purchase')->__('Add Vendor');
        }
    }
}
