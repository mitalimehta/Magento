<?php

class Ebulb_Customsearch_Block_Adminhtml_Searchattribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {  
        $this->_objectId = 'id';
        $this->_controller = 'vendor';
       
        parent::__construct();
        
        $this->_blockGroup = 'customsearch';
        $this->_controller = 'adminhtml_searchattribute';

        $this->_updateButton('save', 'label', Mage::helper('customsearch')->__('Save Search'));
        $this->_updateButton('delete', 'label', Mage::helper('customsearch')->__('Delete Search'));

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
        if( Mage::registry('current_search') && Mage::registry('current_search')->getId() ) {
            return Mage::helper('purchase')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('current_search')->getData('type_label')));
        } else {
            return Mage::helper('purchase')->__('Add Custom Search');
        }
    }
}
