<?php

class Ebulb_Productgroup_Model_Mysql4_Productgroup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productgroup/productgroup');
    }
    
public function prepareSummary()
{
		$this->setConnection($this->getResource()->getReadConnection());
				
		$this->getSelect()
			->from(array('main_table'=>'productgroup'),'*');
			//->where('approved = ?', 1)
			//->order('posted','desc');;
		
		return $this;
}

public function getDetails($testimonial_id)
{
	
		$this->setConnection($this->getResource()->getReadConnection());
		$this->getSelect()
			->from(array('main_table'=>'productgroup'),'*');
			//->where('testimonial_id = ?', $testimonial_id);


		return $this;


}
}