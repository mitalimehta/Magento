<?php

class Ebulb_Productgroup_Block_Adminhtml_Productgroup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('productgroup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('productgroup')->__('Product Groups Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('productgroup')->__('Product Groups Information'),
          'title'     => Mage::helper('productgroup')->__('Product Groups Information'),
          'content'   => $this->getLayout()->createBlock('productgroup/adminhtml_productgroup_edit_tab_form')->toHtml(),
      ));
      
      
      $this->addTab('form_section1', array(
          'label'     => Mage::helper('productgroup')->__('Product Group SKUs'),
          'title'     => Mage::helper('productgroup')->__('Product Group SKUs'),
          'content'   => $this->getLayout()->createBlock('productgroup/adminhtml_productgroup_productsku_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}