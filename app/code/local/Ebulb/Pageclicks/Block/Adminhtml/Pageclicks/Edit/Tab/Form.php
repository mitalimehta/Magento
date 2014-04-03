<?php

class Ebulb_Pageclicks_Block_Adminhtml_Pageclicks_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected $_productEntityTypeId;
    
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(); 
      $this->setForm($form);
      $fieldset = $form->addFieldset('pageclicks_form', array('legend'=>Mage::helper('pageclicks')->__('Homepage Links Information')));
      
    
     $fieldset->addField('page_title', 'text', array(
          'label'     => Mage::helper('pageclicks')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'page_title',
      ));
      
      $fieldset->addField('html_link', 'text', array(
          'label'     => Mage::helper('pageclicks')->__('HTML Link'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'html_link',
      ));
   
/*      $this->setChild('continue_button',
        $this->getLayout()->createBlock('adminhtml/widget_button')
        ->setData(array(
        'label' => Mage::helper('catalog')->__('Add'),
        'class' => 'save',
        'type' => 'button',
        'onclick'   => "GetAtributeValues();",  
        ))
        );
*/   
       $field = $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
       ));  
       
  
    if ( Mage::getSingleton('adminhtml/session')->getPageclicksData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPageclicksData());
          Mage::getSingleton('adminhtml/session')->setPageclicksData(null);
      } elseif ( Mage::registry('pageclicks_data') ) {
          $form->setValues(Mage::registry('pageclicks_data')->getData());
      }
      return parent::_prepareForm();
  }
}