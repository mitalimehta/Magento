<?php

class Ebulb_Purchase_Block_Order_New extends Mage_Adminhtml_Block_Widget_Form
{
		
	public function __construct()
	{
		parent::__construct();
	}
	
	public function GetBackUrl()
	{
		return $this->getUrl('purchase/orders/list', array());
	}

	public function getVendorsAsCombo($name='vendor_id')
	{
		$return = '<select  id="'.$name.'" name="'.$name.'">';

		$collection = Mage::getModel('purchase/vendor')
			->getCollection()
            ->setOrder('vendor_company_name', 'asc');
        
		foreach ($collection as $item)
		{
			$return .= '<option value="'.$item->getVendorId().'">'.$item->getVendorCompanyName().'  ['.$item->getVendorName().'] </option>';
		}
		
		$return .= '</select>';
		return $return;
	}
}
