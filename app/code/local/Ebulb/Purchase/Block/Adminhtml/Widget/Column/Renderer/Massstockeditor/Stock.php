<?php

class Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_MassStockEditor_Stock
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$stock = (int)$row->getstock_qty();
    	$productId = $row->getId();
        $disabled = "";
        if($row->getMasterSku())
           $disabled  = 'disabled = "disabled"';
    	$retour = '<input '.$disabled.' type="text name="stock_'.$productId.'" id="stock_'.$productId.'" value="'.$stock.'" style="width:60px;" disabled>';
		//$retour .= '&nbsp;<input '.$disabled.' type="checkbox" name="ch_stock_'.$productId.'" id="ch_stock_'.$productId.'" value="1" onclick="toggleStockInput('.$productId.');">';
		return $retour;
    }
    
}