<?php

class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  //protected $_productEntityTypeId;
    
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      
      $form->setHtmlIdPrefix('_vendor');
      //$form->setFieldNameSuffix('vendor');
      
      $vendor = Mage::registry('current_vendor');       
      
      $model = Mage::getModel('purchase/vendor');
      
      $fieldset1 = $form->addFieldset('edit_form1', array('legend'=>Mage::helper('purchase')->__('General Information')));
   
      $fieldset1->addField('vendor_name', 'text', array(
          'label'     => Mage::helper('purchase')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'vendor_name',
      ));
      
      $fieldset1->addField('vendor_company_name', 'text', array(
          'label'     => Mage::helper('purchase')->__('Account Name'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'vendor_company_name',
      ));  
      
      $fieldset1->addField('email', 'text', array(
          'label'     => Mage::helper('purchase')->__('Email'),
          'class'     => 'required-entry validate-email',
          'required'  => true,
          'name'      => 'email',
          'type'      => 'email',
      ));
      
      $fieldset1->addField('website', 'text', array(
          'label'     => Mage::helper('purchase')->__('Website'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'website',
      ));
      
      $fieldset1->addField('federal_tax_number', 'text', array(
          'label'     => Mage::helper('purchase')->__('Federal Tax Number'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'federal_tax_number',
      ));
      
      $fieldset1->addField('payment_terms', 'text', array(
          'label'     => Mage::helper('purchase')->__('Payment Terms'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'payment_terms',
      ));
      
      
      $fieldset1->addField('credit_limit', 'text', array(
          'label'     => Mage::helper('purchase')->__('Credit Limit'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'credit_limit',
      ));
      
      $fieldset1->addField('account_name_from_vendor', 'text', array(
          'label'     => Mage::helper('purchase')->__('Account Number'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'account_name_from_vendor',
          'note'      => 'Assigned by Vendor to us',
      ));
      
      
      $fieldset2 = $form->addFieldset('edit_form2', array('legend'=>Mage::helper('purchase')->__('Contact Information')));
    
      $fieldset2->addField('address1', 'text', array(
          'label'     => Mage::helper('purchase')->__('Address 1'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'address1',
      ));
      
      $fieldset2->addField('address2', 'text', array(
          'label'     => Mage::helper('purchase')->__('Address 2'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'address2',
      ));
      
      $fieldset2->addField('zipcode', 'text', array(
          'label'     => Mage::helper('purchase')->__('Zipcode'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'zipcode',
      ));
      
      $fieldset2->addField('city', 'text', array(
          'label'     => Mage::helper('purchase')->__('City'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'city',
      ));
      
      $fieldset2->addField('country', 'select', array(
          'label'     => Mage::helper('purchase')->__('Country'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'country',
          'values'   =>  $model->getCountryOptions(), 
      ));
     
      $fieldset2->addField('state', 'text', array(
          'label'     => Mage::helper('purchase')->__('State'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'state',
      ));
      
      
       $telephone1 =  $fieldset2->addField('telephone1', 'text', array(
          'label'     => Mage::helper('purchase')->__('Telephone 1'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'telephone1',
      ));
      
      $fieldset2->addField('ext1hdn', 'hidden', array(
          'name'      => 'ext1hdn',
      ));
      
      $fieldset2->addField('telephone2hdn', 'hidden', array(
          'name'      => 'telephone2hdn',
      ));
        
       $fieldset2->addField('ext2hdn', 'hidden', array(
          'name'      => 'ext2hdn',
      )); 
      
      $telephone1->setRenderer($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_renderer_telephone'));   
     
      $fieldset2->addField('cellphone', 'text', array(
          'label'     => Mage::helper('purchase')->__('Cellphone'),
          //'class'     => 'validate-phoneStrict',
          'name'      => 'cellphone',
      ));
      
      $fieldset2->addField('other_telephone', 'text', array(
          'label'     => Mage::helper('purchase')->__('Other Telephone'),
          //'class'     => 'validate-phoneStrict',  
          'name'      => 'other_telephone',
      ));
      
      $fieldset2->addField('fax', 'text', array(
          'label'     => Mage::helper('purchase')->__('Fax'),
          //'class'     => 'validate-fax',
          'name'      => 'fax',
      ));
            
      $form->setValues($vendor->getData());
      $this->setForm($form);  
      
       
      $country = $form->getElement('country');
      if ($country) {
           $country->addClass('countries');
      }
      
      if (!$form->getElement('country')->getValue()) {
            $form->getElement('country')->setValue(Mage::helper('core')->getDefaultCountry($this->getStore()));
      }
      
      $form->getElement('ext1hdn')->setValue($vendor->getData('ext1'));
      $form->getElement('telephone2hdn')->setValue($vendor->getData('telephone2'));
      $form->getElement('ext2hdn')->setValue($vendor->getData('ext2')); 
      
      /*$regionElement = $form->getElement('state');
      if ($regionElement) { 
            $regionElement->setRenderer(Mage::getModel('purchase/renderer_region'));
      }*/
      
            
      return parent::_prepareForm();
  }
}