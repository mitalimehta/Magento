<?php

  class Ebulb_Newreports_Block_Adminhtml_Renderer_Shipinsurance extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
  {     
   
      public function render(Varien_Object $row){
            $shipping_description = $row->getData('shipping_description');
            
            $state                   = $row->getData('state');  
            
            if($state != ''){
                    
                $collection = Mage::registry("report_collection"); 
                $total_shipinsurance = 0; 
                      
                if($collection){
              
                    foreach ($collection as $data){
                        $shipping_description = $data->getData('shipping_description');   
                        $shipping_description_array = explode("<br />",$shipping_description);
   
                         foreach($shipping_description_array as $val){
                            if(stristr($val,"Insurance")){
                                $shiptodaystr = $val;
                                $shiptodayarr = explode(" ",trim($shiptodaystr));
                                
                                foreach($shiptodayarr as $string){
                                   
                                    if(strstr($string,"$"))  
                                    { 
                                        $total_shipinsurance += substr($string,1,strlen($string));    
                                        break;       
                                    }  
                                }
                               break;         
                            }
                         }
                    
                    }
                }
                return "$".$total_shipinsurance;              
            }
            
            $shipinsurance = "$0.00";
                                                                          
            $shipping_description_array = explode("<br />",$shipping_description);
   
            foreach($shipping_description_array as $val){
                if(stristr($val,"Insurance")){
                    $shiptodaystr = $val;
                    $shiptodayarr = explode(" ",trim($shiptodaystr));
                    
                    foreach($shiptodayarr as $string){
                       
                        if(strstr($string,"$"))  
                        { 
                            $shipinsurance = $string;    
                            break;       
                        }  
                    }
                   break;         
                }
             } 
            
            return $shipinsurance;
      }
    
  }
?>                                                               



