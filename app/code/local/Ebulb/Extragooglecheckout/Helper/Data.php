 <?php
class Ebulb_Extragooglecheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    function getShippingCodeByDescription($description)
    {
    
       
       $description = eregi_replace("[^A-Z0-9]","",strtoupper($description));
       //return array();
       $am = Mage::getStoreConfig("carriers/fedex/allowed_methods"); 
       $allowed_fedex = explode(',',$am);
       $frm = Mage::getModel('usa/shipping_carrier_fedex_source_method')->toOptionArray();
       foreach($frm as $method){
                    $ship_method[eregi_replace("[^A-Z0-9]","",strtoupper('Federal Express - '.$method['label']))] =  'fedex_'.$method['value'];  
                    $ship_method[eregi_replace("[^A-Z0-9]","",strtoupper('FEDEX - '.$method['label']))] =  'fedex_'.$method['value'];  
          }
          
          $am = Mage::getStoreConfig("carriers/usps/allowed_methods"); 
          $allowed_usps = explode(',',$am);
          $frm = Mage::getModel('usa/shipping_carrier_usps_source_method')->toOptionArray();
          foreach($frm as $method){
                    $ship_method[eregi_replace("[^A-Z0-9]","",strtoupper('United States Postal Service - '.$method['label']))] = 'usps_'.$method['value'];  
                    $ship_method[eregi_replace("[^A-Z0-9]","",strtoupper('USPS - '.$method['label']))] = 'usps_'.$method['value'];  
          } 
          if(isset($ship_method[$description])) return $ship_method[$description]; 
          else return false;
      }
      
    public function log($text, $nl=true)
    {
        //Mage::log($text);
        error_log(print_r($text,1).($nl?"\n":''), 3, Mage::getBaseDir('log').DS.'googlecheckout_shipping.log');
        return $this;
    }

}

      
