<?php

class Ebulb_Testimonial_Block_Adminhtml_Testimonial_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {  
      Mage::getBlockSingleton('lightbox/admin_tasklogin')
          //->setPermission("testimonial/edittestimonial")  
          //->setCondition(true)  
          ->renderForm();
          
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);    
      $this->setForm($form);   
      
      return parent::_prepareForm();
  }
}