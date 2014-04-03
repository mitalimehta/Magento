<?php

class Ebulb_Automaticfeed_Block_Adminhtml_Settings_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  
  protected $_testmodeOptions = array(
                                        '0' => 'No',
                                        '1' => 'Yes'
                                     );
   
  protected $_filetypeOptions = array(
                                        'csv' => 'CSV',
                                        'txt' => 'TEXT'
                                     );
  
  protected $_seperatorOptions = array(
                                        'comma' => 'COMMA',
                                        'tab' => 'TAB'
                                     );
                                      
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
      
      $autofeed = Mage::registry('current_settings');       
      
      $fieldset1 = $form->addFieldset('edit_form1', array('legend'=>Mage::helper('automaticfeed')->__('Company Information')));
      
      $company = Mage::getModel('automaticfeed/company'); 
      $companycollection = $company->getCollection(); 
      
      $attributes = array(); 
      $count      = 1;
      $attributes[0]['value'] = '';
      $attributes[0]['label'] = '   --------- Please Select Company ---------';
      
      foreach($companycollection->getData() as $attribute){ 
        $attributes[$count]['value'] = $attribute['company_id'];
        $attributes[$count]['label'] = $attribute['company_name'];
        $count++;
      } 
      
      $fieldset1->addField('company_id', 'select', array(
          'label'     => Mage::helper('automaticfeed')->__('Company'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'company_id',
          'values'    => $attributes,  
      ));
      
      $stores  = Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash();
       
      $element = $fieldset1->addField('store_id', 'select', array(
           'label'     => Mage::helper('adminhtml')->__('Website'),
           'title'     => Mage::helper('adminhtml')->__('Website'),
           'name'      => 'store_id',
           'required'  => true,
           'values'    => $stores, 
           //'value'     => $formValues['store_id'],
      ));
    
      $fieldset2 = $form->addFieldset('edit_form2', array('legend'=>Mage::helper('automaticfeed')->__('FTP Information')));     
     
      $fieldset2->addField('ftp_host', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('FTP Host'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'ftp_host',
      ));
      
      $fieldset2->addField('ftp_user', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('FTP User'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'ftp_user',
      ));
      
      $fieldset2->addField('ftp_pwd', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('FTP Password'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'ftp_pwd',
          'type'      => 'password',
      )); 
      
      $fieldset2->addField('directory', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('Directory'),  
          'name'      => 'directory',  
      )); 
      
      $fieldset2->addField('ftp_testmode', 'select', array(
          'label'     => Mage::helper('automaticfeed')->__('Test Mode'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'ftp_testmode',
          'values'    => $this->_testmodeOptions,
          'note'      => $this->__('If set to yes, it will just create file and it will not send to shopping company'),
      ));
          
      $fieldset3 = $form->addFieldset('edit_form3', array('legend'=>Mage::helper('automaticfeed')->__('File Information')));     
     
      $fieldset3->addField('file_name', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('File Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'file_name',
          'note'      => $this->__('Please enter file name with an extension ( e.g. test.csv )'),
      ));
      
      
      $fieldset3->addField('file_type', 'select', array(
          'label'     => Mage::helper('automaticfeed')->__('File Type'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'file_type',
          'values'    => $this->_filetypeOptions,
      ));
      
      $fieldset3->addField('logfile_name', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('Log File Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'logfile_name',
          'note'      => $this->__('Please enter file name with an extension ( e.g test.log )'),
      ));
      
      $fieldset3->addField('seperator', 'select', array(
          'label'     => Mage::helper('automaticfeed')->__('Seperator'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'seperator',
          'note'      => $this->__('Either Comma seperated or Tab Seperated'),
          'values'    => $this->_seperatorOptions, 
      ));
      
      $fieldset3->addField('include_header', 'select', array(
          'label'     => Mage::helper('automaticfeed')->__('Include Header'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'include_header',
          'values'    => $this->_testmodeOptions, 
          //'note'      => $this->__('Please enter file name with an extension ( e.g test.log )'),
      ));
      
      $fieldset4 = $form->addFieldset('edit_form4', array('legend'=>Mage::helper('automaticfeed')->__('Attribute Information')));     
          
      $attributeField = $fieldset4->addField('attr_info', 'text', array(
          'label'     => Mage::helper('automaticfeed')->__('Attribute Info'),
          'name'      => 'attr_info',
      ));
      
      $attributeField->setRenderer($this->getLayout()->createBlock('automaticfeed/adminhtml_settings_edit_renderer_attribute'));
            
      $form->setValues($autofeed->getData());
      $this->setForm($form);  
     
      return parent::_prepareForm();
  }
}