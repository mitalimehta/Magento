<?php

class Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_SubtotalCost
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
        return round($row->getAverageCost() * $row->getTotalQty(),2);
    }
    
}