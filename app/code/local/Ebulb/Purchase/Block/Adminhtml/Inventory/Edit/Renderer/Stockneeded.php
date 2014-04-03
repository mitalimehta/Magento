<?php

class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Stockneeded extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){ 
        
        
        $productId = $row->getData('entity_id');
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        //$stock_needed = $row->getData('max_qty') - $row->getData('po_qty') - $row->getData('qty') ;
        $stock_needed = $stockItem->getMaxQty() - $stockItem->getPoQty() - $stockItem->getQty();
        
        $html  = round($stock_needed);
        return $html; 
    }    
}

?>
