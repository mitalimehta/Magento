<?php

class Ebulb_Purchase_Block_Widget_Column_Filter_VendorProduct extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        return $this->getVendorArray();
    }
    
    public function getCondition()
    {
		/*$vendorId = $this->getValue();
		$collection = mage::getModel('purchase/vendorproduct')
							->getCollection()
							->addFieldToFilter('vendor_id', $vendorId);
		$ids = array();
		foreach ($collection as $item)
		{
			$ids[] = $item->getProductId();
		}
		
        if ($this->getValue()) {
        	return array('in' => $ids);
        }*/
    }
    
    /**
     * Return vendors list as array
     *
     */
    public function getVendorArray()
    {
		/*$return = array();
		$return[] = array('label' => '', 'value' => '');
		
		$collection = Mage::getModel('purchase/vendor')
			->getCollection()
			->setOrder('vendor_name', 'asc');
		foreach ($collection as $item)
		{
			$return[] = array('label' => $item->getVendorName(), 'value' => $item->getVendorId());
		}
		return $return;      */
    }
}