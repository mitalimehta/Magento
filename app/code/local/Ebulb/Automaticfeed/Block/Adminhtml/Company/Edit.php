<?php

class Ebulb_Automaticfeed_Block_Adminhtml_Company_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {  
        $this->_objectId = 'id';
        $this->_controller = 'company';
       
        parent::__construct();
        
        $this->_blockGroup = 'automaticfeed';
        $this->_controller = 'adminhtml_company';

        $this->_updateButton('save', 'label', Mage::helper('customer')->__('Save Company'));
        $this->_updateButton('delete', 'label', Mage::helper('customer')->__('Delete Company'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productgroup_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'automaticfeed_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'automaticfeed_content');
                }
            }

            function saveAndContinueEdit(){    
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('current_company') && Mage::registry('current_company')->getId() ) {
            return Mage::helper('automaticfeed')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('current_company')->getData('company_name')));
        } else {
            return Mage::helper('automaticfeed')->__('Add Company');
        }
    }
}
