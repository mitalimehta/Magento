<?php  

class Ebulb_Pageclicks_Block_Adminhtml_Pageclicks extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_Pageclicks';
    $this->_blockGroup = 'pageclicks';
    $this->_headerText = Mage::helper('productgroup')->__('Home page Links Manager');
    $this->_addButtonLabel = Mage::helper('pageclicks')->__('Add new link');
    parent::__construct();
  }
}