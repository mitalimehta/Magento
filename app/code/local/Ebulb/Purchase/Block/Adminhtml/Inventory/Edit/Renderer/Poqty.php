<?php

class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Poqty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){ 
        
        $productId = $row->getData('entity_id');
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        $po_qty = $stockItem->getPoQty();
        $html  = round($po_qty);
        return $html; 
    }    
}

?>
