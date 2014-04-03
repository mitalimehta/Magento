<?php

class Ebulb_Automaticfeed_Block_Adminhtml_Company_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  //protected $_productEntityTypeId;
    
  protected function _prepareForm()
  { 
       $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
                                      'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);
      
      $form->setHtmlIdPrefix('_vendor');
      //$form->setFieldNameSuffix('vendor');
      
      $company = Mage::registry('current_company');       
      
      //$model = Mage::getModel('purchase/contact');
      
      $fieldset1 = $form->addFieldset('edit_form1', array('legend'=>Mage::helper('automaticfeed')->__('Company Information')));
    
      $fieldset1->addField('company_name', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('Company Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'company_name',
      ));
      
      $fieldset1->addField('company_website', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('Company Website'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'company_website',
      ));
     
            
      $form->setValues($company->getData());
      $this->setForm($form);  
     
      return parent::_prepareForm();
  }
}