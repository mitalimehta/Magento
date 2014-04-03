<?php

class Ebulb_Testimonial_ListController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();     
		$this->renderLayout();
	}
}