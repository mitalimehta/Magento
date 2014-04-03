<?php

class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Quantity extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){ 
        
        $so_qty = round($row->getData('product_qty'));
        
        if($so_qty >= 0)
            $html  = "<font color='green'>".$so_qty."</font>";
        else
            $html  = "<font color='red'>".$so_qty."</font>";
            
        return $html; 
    }    
}

?>
