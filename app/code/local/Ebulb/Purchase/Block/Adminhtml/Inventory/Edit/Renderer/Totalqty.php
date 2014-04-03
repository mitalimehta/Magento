<?php

class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Totalqty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){ 
        
        $productId = $row->getData('entity_id');
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        $total_qty = $stockItem->getTotalQty();
        $html  = round($total_qty);
        return $html; 
    }    
}

?>
