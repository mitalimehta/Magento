<?php
  class Ebulb_Newreports_Block_Adminhtml_Renderer_Rep extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
  {     
   
      public function render(Varien_Object $row){
                $order_rep = $row->getData('order_rep');
                if( $order_rep == 'Direct')
                    $order_rep = "Web - ".strtoupper(Mage::app()->getStore($row->getData('store_id'))->getCode());   //$row->getData('store_id');    //Mage::getModel('code/store')->getData('code');
                return $order_rep;
      }
    
  }
?>                                                               



