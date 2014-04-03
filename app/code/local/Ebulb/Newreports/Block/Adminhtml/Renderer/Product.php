<?php

  class Ebulb_Newreports_Block_Adminhtml_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
  {     
   
      public function render(Varien_Object $row){
            
            $html = ''; 
            $product_sku = $row->getData('product_sku'); 
            $product_name = $row->getData('product_name'); 
            $available_qty = $row->getData('available_qty'); 
            
            $html .= "<b>".$product_name."<br />";    
            $html .= "SKU : ".$product_sku."<br />";
           
            $html .= "Available Stock : ".round($available_qty)."</b>";
            return $html;
      }
    
  }
?>                                                               



