<?php

class Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_MassStockEditor_Cost
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
        $stock = round($row->getCost(),2);
        $productId = $row->getId();
        $disabled = "";
        if($row->getMasterSku())
           $disabled  = 'disabled = "disabled"';
        $retour = '<input '.$disabled.' type="text name="cost_'.$productId.'" id="cost_'.$productId.'" value="'.$stock.'"  style="width:60px;" disabled>';
        //$retour .= '&nbsp;<input '.$disabled.' type="checkbox" name="ch_cost_'.$productId.'" id="ch_cost_'.$productId.'" value="1" onclick="toggleCostInput('.$productId.');">';
        return $retour;
    }
    
}