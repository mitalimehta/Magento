<?php
  
  
class Ebulb_Automaticfeed_Block_Adminhtml_Create extends Mage_Adminhtml_Block_Widget_Form
{  
    public function __construct()
    {  
        parent::_construct();
        $this->setTemplate('automaticfeed/create/form.phtml');
    }

} 