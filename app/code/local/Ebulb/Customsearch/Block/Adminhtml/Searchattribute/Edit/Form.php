<?php

class Ebulb_Customsearch_Block_Adminhtml_Searchattribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _construct()
  {     
      parent::_construct();
      $this->setTemplate('customsearch/searchattribute/form.phtml');
  }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save',array('id' => $this->getRequest()->getParam('id')));
    }
}