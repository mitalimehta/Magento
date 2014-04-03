<?php

class Ebulb_Pageclicks_Block_Adminhtml_Pageclicks_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('pageclicks_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('pageclicks')->__('Homepage Links Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('pageclicks')->__('Homepage Links Information'),
          'title'     => Mage::helper('pageclicks')->__('Homepage LinksInformation'),
          'content'   => $this->getLayout()->createBlock('pageclicks/adminhtml_pageclicks_edit_tab_form')->toHtml(),
      ));
      
      
      /*$this->addTab('form_section1', array(
          'label'     => Mage::helper('productgroup')->__('Product Group SKUs'),
          'title'     => Mage::helper('productgroup')->__('Product Group SKUs'),
          'content'   => $this->getLayout()->createBlock('productgroup/adminhtml_productgroup_productsku_tab_form')->toHtml(),
      ));*/
     
      return parent::_beforeToHtml();
  }
}