<?php

class Ebulb_Purchase_Block_Adminhtml_Contact_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {  
        $this->_objectId = 'id';
        $this->_controller = 'contact';
       
        parent::__construct();
        
        $this->_blockGroup = 'purchase';
        $this->_controller = 'adminhtml_contact';

        $this->_updateButton('save', 'label', Mage::helper('customer')->__('Save Contact'));
        $this->_updateButton('delete', 'label', Mage::helper('customer')->__('Delete Contact'));

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
        if( Mage::registry('current_contact') && Mage::registry('current_contact')->getId() ) {
            return Mage::helper('purchase')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('current_contact')->getData('first_name')));
        } else {
            return Mage::helper('purchase')->__('Add Contact');
        }
    }
}
