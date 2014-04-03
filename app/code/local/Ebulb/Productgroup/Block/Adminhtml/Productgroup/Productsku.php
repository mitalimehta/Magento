<?php

class Ebulb_Productgroup_Block_Adminhtml_Productgroup_Productsku extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'productgroup';
        $this->_controller = 'adminhtml_Productgroup';
        
        $this->_updateButton('save', 'label', Mage::helper('productgroup')->__('Save Product Group SKUs'));
        //$this->_updateButton('delete', 'label', Mage::helper('productgroup')->__('Delete Product Group'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productgroup_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productgroup_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productgroup_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('productgroup_data') && Mage::registry('productgroup_data')->getId() ) {
            return Mage::helper('productgroup')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('productgroup_data')->getData('GroupName')));
        } else {
            return Mage::helper('productgroup')->__('Add Product Group');
        }
    }
}