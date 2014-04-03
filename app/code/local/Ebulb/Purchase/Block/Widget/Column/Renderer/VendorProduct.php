<?php

/*
* Display all suppliers for one product
*/
class Ebulb_Purchase_Block_Widget_Column_Renderer_VendorProduct
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
		$return = '';
		$collection = Mage::getModel('purchase/vendorproduct')
                        ->getCollection()
						->addFieldToFilter('product_id', $row->getId())
						->join('purchase/vendor', '`purchase/vendor`.vendor_id=main_table.vendor_id');
   	
		foreach($collection as $vendor)
		{
			$return .= $vendor->getVendorName().', ';
		}
		$return = substr($return, 0, strlen($return) - 2);
		
        return $return;
    }
    
}