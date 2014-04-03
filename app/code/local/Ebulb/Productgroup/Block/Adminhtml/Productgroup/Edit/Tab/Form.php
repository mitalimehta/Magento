<?php

class Ebulb_Productgroup_Block_Adminhtml_Productgroup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected $_productEntityTypeId;
    
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(); 
      $this->setForm($form);
      $fieldset = $form->addFieldset('productgroup_form', array('legend'=>Mage::helper('productgroup')->__('Product Groups Information')));
      
      $this->_productEntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();

      $attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')  
                            ->setEntityTypeFilter($this->_productEntityTypeId)
                            ->addFieldToFilter('frontend_input', array('select','multiselect')) 
                            ->getData();                     
     
      $attributes = array(); 
      $count      = 1;
      $attributes[0]['value'] = '';
      $attributes[0]['label'] = 'Select Attribute';
      
      foreach($attributesInfo as $attribute){ 
        $attributes[$count]['value'] = $attribute['attribute_id']."##########".$attribute['attribute_code'];
        $attributes[$count]['label'] = $attribute['frontend_label'];
        $count++;
      }
       
     $fieldset->addField('GroupName', 'text', array(
          'label'     => Mage::helper('productgroup')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'GroupName',
      ));

      $fieldset->addField('attributes', 'select', array(
          'label'     => Mage::helper('productgroup')->__('Attributes'),
          'name'      => 'attributes[]',
          'values'    => $attributes,
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
       
    /*   $newFieldset = $form->addFieldset(
                'noemailfieldset',
                array('legend'=>Mage::helper('productgroup')->__('Automatic Email'))
            );
        $field = $newFieldset->addField('noemail', 'checkbox',
                array(
                    'label' => Mage::helper('productgroup')->__('Use Auto Generated Email'),
                    'class' => 'input-checkbox',
                    'name'  => 'noemail',
                    'onclick' => 'autoemailid();',
                    'required' => false
                )
            );   */
                
                
       $field->setRenderer($this->getLayout()->createBlock('productgroup/adminhtml_productgroup_edit_renderer_attribute'));
       
    if ( Mage::getSingleton('adminhtml/session')->getProductgroupData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getProductgroupData());
          Mage::getSingleton('adminhtml/session')->setProductgroupData(null);
      } elseif ( Mage::registry('productgroup_data') ) {
          $form->setValues(Mage::registry('productgroup_data')->getData());
      }
      return parent::_prepareForm();
  }
}