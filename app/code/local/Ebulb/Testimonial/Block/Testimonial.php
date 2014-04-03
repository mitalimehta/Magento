<?php
class Ebulb_Testimonial_Block_Testimonial extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTestimonial()     
     { 
        if (!$this->hasData('testimonial')) {
            $this->setData('testimonial', Mage::registry('testimonial'));
        }
        return $this->getData('testimonial');
        
    }
}