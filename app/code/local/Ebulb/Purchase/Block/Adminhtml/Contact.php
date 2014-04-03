<?php
  
  
class Ebulb_Purchase_Block_Adminhtml_Contact extends Mage_Adminhtml_Block_Widget_Grid_Container
{  
    public function __construct()
    {  
        $this->_controller = 'adminhtml_contact';
        $this->_blockGroup = 'purchase';
        $this->_headerText = Mage::helper('purchase')->__('Manage Contacts');
        $this->_addButtonLabel = Mage::helper('purchase')->__('Add New Contact');
        parent::__construct();
    }

}
  
  
  
?>
