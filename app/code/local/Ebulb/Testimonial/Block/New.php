<?php

class Ebulb_Testimonial_Block_New extends Mage_Core_Block_Template{


	public function _construct(){
		
		}

    public function getAction()
    {
        return Mage::getUrl('testimonial/new/save');
    }	
}