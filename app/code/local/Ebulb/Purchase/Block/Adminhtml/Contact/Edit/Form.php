<?php

class Ebulb_Purchase_Block_Adminhtml_Contact_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
      
      $contact = Mage::registry('current_contact');       
      
      //$model = Mage::getModel('purchase/contact');
      
      $fieldset1 = $form->addFieldset('edit_form1', array('legend'=>Mage::helper('purchase')->__('Contact Information')));
   
      $vendor = Mage::getModel('purchase/vendor'); 
      $vendorcollection = $vendor->getCollection(); 
      
      $attributes = array(); 
      $count      = 1;
      $attributes[0]['value'] = '';
      $attributes[0]['label'] = '   --------------- Please Select Vendor ---------------';
      
      foreach($vendorcollection->getData() as $attribute){ 
        $attributes[$count]['value'] = $attribute['vendor_id'];
        $attributes[$count]['label'] = $attribute['vendor_name'];
        $count++;
      } 
      
      $fieldset1->addField('vendor_id', 'select', array(
          'label'     => Mage::helper('purchase')->__('Vendor'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'vendor_id',
          'values'    => $attributes,  
      ));
   
      $fieldset1->addField('first_name', 'text', array(
          'label'     => Mage::helper('purchase')->__('First Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'first_name',
      ));
      
      $fieldset1->addField('middle_name', 'text', array(
          'label'     => Mage::helper('purchase')->__('Middle Name'),
          //'class'     => 'required-entry',
          //'required'  => true,
          'name'      => 'middle_name',
      ));
      
      $fieldset1->addField('last_name', 'text', array(
          'label'     => Mage::helper('purchase')->__('Last Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'last_name',
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
      
   
       $telephone1 =  $fieldset1->addField('telephone1', 'text', array(
          'label'     => Mage::helper('purchase')->__('Telephone 1'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'telephone1',
      ));
      
      $fieldset1->addField('ext1hdn', 'hidden', array(
          'name'      => 'ext1hdn',
      ));
      
      $fieldset1->addField('telephone2hdn', 'hidden', array(
          'name'      => 'telephone2hdn',
      ));
        
       $fieldset1->addField('ext2hdn', 'hidden', array(
          'name'      => 'ext2hdn',
      )); 
      
      $telephone1->setRenderer($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_renderer_telephone'));   
     
      $fieldset1->addField('cellphone', 'text', array(
          'label'     => Mage::helper('purchase')->__('Cellphone'),
          'class'     => 'validate-phoneStrict',
          'name'      => 'cellphone',
      ));
   
      $fieldset1->addField('fax', 'text', array(
          'label'     => Mage::helper('purchase')->__('Fax'),
          'class'     => 'validate-fax',
          'name'      => 'fax',
      ));
            
      $fieldset1->addField('comments', 'textarea', array(
          'label'     => Mage::helper('purchase')->__('Comments'),
          'name'      => 'comments',
      ));      
            
      $form->setValues($contact->getData());
      $this->setForm($form);  
      
      $form->getElement('ext1hdn')->setValue($contact->getData('ext1'));
      $form->getElement('telephone2hdn')->setValue($contact->getData('telephone2'));
      $form->getElement('ext2hdn')->setValue($contact->getData('ext2')); 
      
      /*$regionElement = $form->getElement('state');
      if ($regionElement) { 
            $regionElement->setRenderer(Mage::getModel('purchase/renderer_region'));
      }*/
      
            
      return parent::_prepareForm();
  }
}