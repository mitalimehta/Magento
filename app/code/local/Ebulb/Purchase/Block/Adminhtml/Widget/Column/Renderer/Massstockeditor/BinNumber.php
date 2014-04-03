<?php

class Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_MassStockEditor_BinNumber
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
        $binNo = $row->getwarehouse_bin_number();
        $productId = $row->getId();
        $disabled = '';
        if($row->getMasterSku())
           $disabled  = 'disabled = "disabled"';
        $retour = '<input '.$disabled.' type="text name="binNo_'.$productId.'" id="binNo_'.$productId.'" value="'.$binNo.'" style="width:130px;" disabled>';
        //$retour .= '&nbsp;<input '.$disabled.' type="checkbox" name="ch_binNo_'.$productId.'" id="ch_binNo_'.$productId.'" value="1" onclick="toggleBinNoInput('.$productId.');">';
        return $retour;
    }
    
}