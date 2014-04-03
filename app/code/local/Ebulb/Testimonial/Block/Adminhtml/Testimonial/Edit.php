<?php

class Ebulb_Testimonial_Block_Adminhtml_Testimonial_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'testimonial';
        $this->_controller = 'adminhtml_testimonial';
        
        $this->_updateButton('save', 'label', Mage::helper('testimonial')->__('Save Testimonial'));
        $this->_updateButton('delete', 'label', Mage::helper('testimonial')->__('Delete Testimonial'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('testimonial_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'testimonial_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'testimonial_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        
        $tasklogin = Mage::getBlockSingleton('lightbox/admin_tasklogin')
               ->setTitle("Testimonial - AUTORIZATION")
               ->setCondition(true); 
        
        $editaction = $tasklogin->setPermission("testimonial/edittestimonial")
                            ->setJsAction("editForm.submit();")
                            ->getJsFinalAction(); 
      
        $this->_updateButton('save', 'onclick', $editaction);
        
        $saveandeditaction = $tasklogin->setPermission("testimonial/edittestimonial")
                            ->setJsAction("saveAndContinueEdit();")
                            ->getJsFinalAction(); 
      
        $this->_updateButton('saveandcontinue', 'onclick', $saveandeditaction);
        
        /*if( $this->getRequest()->getParam('ret', false) == 'pending' ) {
            $returnurl = 'pending';    
        } */
        
        $deleteaction = $tasklogin->setPermission("testimonial/edittestimonial")
                            ->setJsAction('deleteConfirm(\'' . Mage::helper('review')->__('Are you sure you want to do this?') . '\', \'' . $this->getUrl('*/*/delete', array(
                                            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                                            //'ret'           => $returnurl,
                                         )) .'\')')
                            ->getJsFinalAction(); 
         
        $this->_updateButton('delete', 'onclick', $deleteaction);  
    }

    public function getHeaderText()
    {
        if( Mage::registry('testimonial_data') && Mage::registry('testimonial_data')->getId() ) {
            return Mage::helper('testimonial')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('testimonial_data')->getTitle()));
        } else {
            return Mage::helper('testimonial')->__('Add Testimonial');
        }  
    }
}