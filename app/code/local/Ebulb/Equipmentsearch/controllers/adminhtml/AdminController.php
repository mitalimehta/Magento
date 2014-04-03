<?php

class Ebulb_Automativesearch_AdminController extends Mage_Adminhtml_Controller_Action
{
   
    public function indexAction()
    {
           $this->loadLayout()
            ->_addContent($this->getLayout()->createBlock('zipcodes/admin_main'))
            ->renderLayout(); 
    }

}