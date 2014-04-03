<?php

class Ebulb_Automativesearch_Block_Adminhtml_Automotivebulbs_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _construct()
  {     
      parent::_construct();
      $this->setTemplate('automativesearch/automotivebulbs/form.phtml');
  }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save',array('id' => $this->getRequest()->getParam('id')));
    }
                                                                                                                    
}