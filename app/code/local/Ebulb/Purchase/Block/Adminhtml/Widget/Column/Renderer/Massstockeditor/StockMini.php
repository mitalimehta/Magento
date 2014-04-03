<?php

class Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_MassStockEditor_StockMini
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$stockMini = (int)$row->getreal_notify_stock_qty();
    	$productId = $row->getId();
        $disabled = '';
        if($row->getMasterSku())
           $disabled  = 'disabled = "disabled"';
    	$retour = '<input '.$disabled.' type="text name="stockmini_'.$productId.'" id="stockmini_'.$productId.'" value="'.$stockMini.'" size="4" disabled>';
		//$retour .= '&nbsp;<input '.$disabled.' type="checkbox" name="ch_stockmini_'.$productId.'" id="ch_stockmini_'.$productId.'" value="1" onclick="toggleStockMiniInput('.$productId.');">';
		return $retour;
    }
    
}