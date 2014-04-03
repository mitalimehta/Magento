<?php

class Ebulb_Newreports_Block_Adminhtml_Renderer_Costtotal extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract                
{
     public function render(Varien_Object $row){ 
         
            $orderstatus = $row->getData('order_state');    
            if($orderstatus != ''){  
                $collection = Mage::registry("report_collection");  
                  $total = 0; 
                  
                  if($collection){
                  
                    foreach ($collection as $data){
                           
                        $total_order             = $data->getData('product_cost'); 
                        $total += $total_order; 
                    }  
                  }
                  
                return "$".round($total,2); 
            }
            else    
                return  "$".round($row->getData('product_cost'),2);       
          
      }
    
    
}

  
?>
