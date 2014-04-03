<?php

class Ebulb_Testimonial_Block_Links extends Mage_Page_Block_Template_Links
{
   
    public function addTestimonialLink()
    {
        		
		$text = $this->__('Noticias');
		
		$this->addLink($text, 'testimonial/list', $text, true, array(), 30, null, 'class="top-link-wishlist"');
		
        return $this;
    }
}