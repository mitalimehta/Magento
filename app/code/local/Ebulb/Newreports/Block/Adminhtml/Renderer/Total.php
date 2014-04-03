<?php

class Ebulb_Newreports_Block_Adminhtml_Renderer_Total extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract                
{
     public function render(Varien_Object $row){  
                return  $row->getData($this->getColumn()->getHeader());       
                /*$total_order             = $row->getData('total_order');       //  (8)
                
                if($orderstatus != ''){
                    
                      $collection = Mage::registry("report_collection");   
         
                      $total_margin = 0; 
                      
                      if($collection){
                      
                        foreach ($collection as $data){
                               
                            $total_order             = $data->getData('total_order');       //  (8)
                            $shipping_amt            = $data->getData('shipping_amt');      //  (9) 
                            $shipping_cost           = $data->getData('shipping_cost');     //  (9.5) 
                            $shipping_today          = $data->getData('shipping_today');    //  (10) 
                            $discount_amount         = $data->getData('discount_amount');   //  (8.5) 
                            $order_insurance         = $data->getData('order_insurance');   //  (11) 
                            $order_tax               = $data->getData('order_tax');         //  (12) 
                            $order_cost              = $data->getData('order_cost_total');        //  (7) 
                          //$status                  = $row->getData('status');        //  (7) 
                             $orderstatus             = $row->getData('order_state');            
                             
                            if($order_cost != 0)
                            {
                              
                                $order_margin = round((($total_order - $order_cost)/$order_cost)*100,2);
                            }
                            else
                                $order_margin = 0; 
                            
                            $total_margin += $order_margin; 
                        }  
                      }
                      
                    return "$".$total_margin;
                }
                else  
                    return "$".$total_order;*/
      }
    
    
}

  
?>
