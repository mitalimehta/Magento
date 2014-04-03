<?php  

class Ebulb_Productgroup_Block_Adminhtml_Productgroup extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_Productgroup';
    $this->_blockGroup = 'productgroup';
    $this->_headerText = Mage::helper('productgroup')->__('Product Group Manager');
    $this->_addButtonLabel = Mage::helper('productgroup')->__('Add new group');
    parent::__construct();
  }
}