<?php

class Ebulb_Pageclicks_Block_Adminhtml_Pageclicks_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'pageclicks';
        $this->_controller = 'adminhtml_Pageclicks';
        
        $this->_updateButton('save', 'label', Mage::helper('pageclicks')->__('Save Link'));
        $this->_updateButton('delete', 'label', Mage::helper('pageclicks')->__('Delete Link'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('pageclicks_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'pageclicks_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'pageclicks_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('pageclicks_data') && Mage::registry('pageclicks_data')->getId() ) {
            return Mage::helper('pageclicks')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('pageclicks_data')->getData('page_title')));
        } else {
            return Mage::helper('pageclicks')->__('Add New Link');
        }
    }
}