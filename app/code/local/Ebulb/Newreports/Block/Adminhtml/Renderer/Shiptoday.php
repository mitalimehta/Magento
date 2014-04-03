<?php

  class Ebulb_Newreports_Block_Adminhtml_Renderer_Shiptoday extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
  {     
   
      public function render(Varien_Object $row){
         
            $shipping_description = $row->getData('shipping_description');
            $state                   = $row->getData('state');  
            
            if($state != ''){
                    
                $collection = Mage::registry("report_collection"); 
                $total_shiptoday = 0; 
                      
                if($collection){
              
                    foreach ($collection as $data){
                        $shipping_description = $data->getData('shipping_description');   
                        $shipping_description_array = explode("<br />",$shipping_description);
   
                         foreach($shipping_description_array as $val){
                            if(stristr($val,"Ship today")){
                                $shiptodaystr = $val;
                                $shiptodayarr = explode(" ",trim($shiptodaystr));
                                
                                foreach($shiptodayarr as $string){
                                   
                                    if(strstr($string,"$"))  
                                    { 
                                        $total_shiptoday += substr($string,1,strlen($string));    
                                        break;       
                                    }  
                                }
                               break;         
                            }
                         }
                    
                    }
                }
                return "$".$total_shiptoday;              
            }
            
            $shiptoday = "$0.00";
           
            $shipping_description_array = explode("<br />",$shipping_description);
   
             foreach($shipping_description_array as $val){
                if(stristr($val,"Ship today")){
                    $shiptodaystr = $val;
                    $shiptodayarr = explode(" ",trim($shiptodaystr));
                    
                    foreach($shiptodayarr as $string){
                       
                        if(strstr($string,"$"))  
                        { 
                            $shiptoday = $string;    
                            break;       
                        }  
                    }
                   break;         
                }
             } 
            
            return $shiptoday;
      }
    
  }
?>                                                               



