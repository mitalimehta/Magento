<?php

class Ebulb_Testimonial_Block_Details extends Mage_Core_Block_Template{

	protected $_testimonialCollection = null;


	protected function _prepareLayout()
	{
		$this->getLayout()->createBlock('testimonial/breadcrumbs');
		return parent::_prepareLayout();
	}

	/**
     * Recogemos la collection de actualidad
     *
     */
	public function getLoadedTestimonialCollection()
	{
		return $this->_getTestimonialCollection();
	}

	/** Devuelve el detalle de noticia de actualidad
     *
     */
	public function _getTestimonialCollection()
	{
		$testimonial_id = $this->getRequest()->getParam('id');
			
  		if($testimonial_id != null && $testimonial_id != '')	{
			$testimonial = Mage::getModel('testimonial/testimonial')->load($testimonial_id)->getData();
		} else {
			$testimonial = null;
		}	
		
		if($testimonial == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$testimonialTable = $resource->getTableName('testimonial');
			
			$select = $read->select()
			   ->from($testimonialTable,array('testimonial_id','title','testimonial_text','approved','fullname','city','state','posted'))
			   ->where('approved',1);
			   
			   
			$testimonial = $read->fetchRow($select);
			
		}
						
		
		return $testimonial;
	}
	
	
	
}