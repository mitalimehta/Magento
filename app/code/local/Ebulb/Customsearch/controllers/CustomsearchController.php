<?php

class Ebulb_Customsearch_CustomsearchController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    { 
        $this->loadLayout();     
        $this->renderLayout();  
    }
}