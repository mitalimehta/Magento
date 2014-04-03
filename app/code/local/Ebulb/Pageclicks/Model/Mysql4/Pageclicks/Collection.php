<?php

class Ebulb_Pageclicks_Model_Mysql4_Pageclicks_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pageclicks/pageclicks');
    }
    
public function prepareSummary()
{
        $this->setConnection($this->getResource()->getReadConnection());
                
        $this->getSelect()
            ->from(array('main_table'=>'page_links'),'*');
            //->where('approved = ?', 1)
            //->order('posted','desc');;
        
        return $this;
}

public function getDetails($testimonial_id)
{
    
        $this->setConnection($this->getResource()->getReadConnection());
        $this->getSelect()
            ->from(array('main_table'=>'page_links'),'*');
            //->where('testimonial_id = ?', $testimonial_id);


        return $this;


}
}