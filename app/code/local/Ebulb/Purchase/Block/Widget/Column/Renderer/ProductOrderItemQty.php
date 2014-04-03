<?php

class Ebulb_Purchase_Block_Order_Widget_Column_Renderer_ProductOrderItemQty
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	//retrieve information
    	$productId = $row->getProductId();
        
    	$collection = Mage::getModel('purchase/orderitem')
                            ->getCollection()  
                            ->addFieldToFilter('product_id', $productId);
                            //->addExpressionAttributeToSelect('po_qty',
                            //    'sum(({{pop_qty}} - {{pop_supplied_qty}}))',
                            //     array('pop_qty', 'pop_supplied_qty'));
                                 
        $collection->getSelect()
                   ->group("product_id");
        $collection->load();
                                      
    	//return value
    	$retour = 0;
    	foreach ($collection as $item)
		         $retour += $item->getPoQty();
                      	
		return number_format($retour,0,"",".");
    }
    
}