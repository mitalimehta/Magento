<?php

class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Salesorderqty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){ 
        
        $productId = $row->getData('entity_id');
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
        //$so_qty = $row->getData('total_qty') - $row->getData('qty');
        $so_qty = $stockItem->getTotalQty() - $stockItem->getQty();
        $html  = round($so_qty);
        return $html; 
    }    
}

?>
