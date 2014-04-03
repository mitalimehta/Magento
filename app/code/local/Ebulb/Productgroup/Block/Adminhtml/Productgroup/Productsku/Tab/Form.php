<?php

class Ebulb_Productgroup_Block_Adminhtml_Productgroup_Productsku_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected $_productEntityTypeId;
    
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(); 
      $this->setForm($form);
      $fieldset = $form->addFieldset('productgroup_form', array('legend'=>Mage::helper('productgroup')->__('Combination of Values')));
      
      /*$this->_productEntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();

      $attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')  
                            ->setEntityTypeFilter($this->_productEntityTypeId)
                            ->addFieldToFilter('frontend_input', 'select') 
                            ->getData();
     
      $attributes = array(); 
      $count      = 1;
      $attributes[0]['value'] = '';
      $attributes[0]['label'] = 'Select Attribute';
      
      foreach($attributesInfo as $attribute){ 
        $attributes[$count]['value'] = $attribute['attribute_id']."##########".$attribute['attribute_code'];
        $attributes[$count]['label'] = $attribute['frontend_label'];
        $count++;
      }*/
       
     /*$fieldset->addField('GroupName', 'text', array(
          'label'     => Mage::helper('productgroup')->__('Name'),
          'class'     => 'required-entry', 
          'name'      => 'GroupName',
          'readonly'      => 'readonly',
      ));*/ 
      
      /*$newFieldset = $form->addFieldset(
                'attributes',
                array('legend'=>Mage::helper('productgroup')->__('Product SKUs'))
            );*/
        $field = $fieldset->addField('noemail', 'checkbox',
                array(
                    'label' => Mage::helper('productgroup')->__('Attributes'),  
                    'name'  => 'attributes',
                    'required' => false
                )
            );   
                
                
       $field->setRenderer($this->getLayout()->createBlock('productgroup/adminhtml_productgroup_productsku_renderer_attribute'));
       
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