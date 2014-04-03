<?php

class Ebulb_Testimonial_Block_List extends Mage_Core_Block_Template{


	public function _construct(){
		
		}
	
	protected $_testimonialCollection = null;

	
	/**
     * Recogemos la collection de actualidad
     *
     */
	public function getLoadedTestimonialCollection()
	{
		return $this->_getTestimonialCollection();
	}

	
	/** Devuelve el listado de noticias de actualidad
     *
     */
	public function _getTestimonialCollection()
	{
			if (is_null($this->_testimonialCollection)) {
				//$now = date('Y-m-d');
				$now = now();
				//$this->_actualidadCollection = Mage::getModel('actualidad/actualidad')->getCollection()->prepareSummary();
				$this->_testimonialCollection = Mage::getModel('testimonial/testimonial')->getCollection();
			
				$this->_testimonialCollection->getSelect('*');
				$this->_testimonialCollection->addFieldToFilter('approved', '1')
//					->addFieldToFilter('fechaini', array('to' => $now))
//					->addFieldToFilter('fechafin', array(array('from' => $now), array('null'=> 1)))
					->setOrder('posted','desc');

			}

		return $this->_testimonialCollection;
	}

	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
	public function _beforeToHtml()
	{
		$toolbar = $this->getLayout()->createBlock('testimonial/toolbar', microtime());

		$toolbar->setCollection($this->_getTestimonialCollection());
		$this->setChild('toolbar', $toolbar);
		Mage::dispatchEvent('testimonial_block_list_collection', array(
		'collection'=>$this->_getTestimonialCollection(),
		));

		$this->_getTestimonialCollection()->load();
		return parent::_prepareLayout();
	}

	/**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}
}