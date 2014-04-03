<?php

class Ebulb_Automativesearch_Block_Adminhtml_Automotivebulbs_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {  
        $this->_objectId = 'id';
        $this->_controller = 'automotivebulbs';
       
        parent::__construct();
        
        $this->_blockGroup = 'automativesearch';
        $this->_controller = 'adminhtml_automotivebulbs';

        $this->_updateButton('save', 'label', Mage::helper('automativesearch')->__('Save Search'));
        $this->_updateButton('delete', 'label', Mage::helper('automativesearch')->__('Delete Search'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productgroup_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'customsearch_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'customsearch_content');
                }
            }

            function saveAndContinueEdit(){  
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('current_model') && Mage::registry('current_model')->getId() ) {
            return Mage::helper('automativesearch')->__("Edit Item ");
        } else {
            return Mage::helper('automativesearch')->__('Add Search');
        }
    }
}
