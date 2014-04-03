<?php

class Ebulb_Testimonial_Block_Adminhtml_Testimonial_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('testimonial_form', array('legend'=>Mage::helper('testimonial')->__('Testimonial information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('testimonial')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

     //no file upload
	/*  $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('testimonial')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));*/
	 //no dates rquired
/*	  $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
	  
        $fieldset->addField('fechaini', 'date', array(
            'name'   => 'fechaini',
            'label'  => Mage::helper('testimonial')->__('Inicio'),
            'title'  => Mage::helper('testimonial')->__('Inicio'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
			'required'  => true
        ));
        $fieldset->addField('fechafin', 'date', array(
            'name'   => 'fechafin',
            'label'  => Mage::helper('testimonial')->__('Fin'),
            'title'  => Mage::helper('testimonial')->__('Fin'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
			'required'  => true
        ));*/
	
      $fieldset->addField('approved', 'select', array(
          'label'     => Mage::helper('testimonial')->__('Status'),
          'name'      => 'approved',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('testimonial')->__('Approved'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('testimonial')->__('Declined'),
              ),
          ),
      ));
     
      $fieldset->addField('testimonial_text', 'editor', array(
          'name'      => 'testimonial_text',
          'label'     => Mage::helper('testimonial')->__('Content'),
          'title'     => Mage::helper('testimonial')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      $fieldset->addField('fullname', 'text', array(
          'label'     => Mage::helper('testimonial')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'fullname',
      ));
      
      $fieldset->addField('city', 'text', array(
          'label'     => Mage::helper('testimonial')->__('City'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'city',
      ));
      
      $fieldset->addField('state', 'text', array(
          'label'     => Mage::helper('testimonial')->__('State'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'state',
      ));
      
     
      if ( Mage::getSingleton('adminhtml/session')->getTestimonialData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTestimonialData());
          Mage::getSingleton('adminhtml/session')->setTestimonialData(null);
      } elseif ( Mage::registry('testimonial_data') ) {
          $form->setValues(Mage::registry('testimonial_data')->getData());
      }
      return parent::_prepareForm();
  }
}