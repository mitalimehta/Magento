<?php

class Ebulb_Pageclicks_Helper_Data extends Mage_Core_Helper_Abstract
{
     public function getuserip()
    {
        if (isset($_SERVER))
       {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
                return $_SERVER["HTTP_X_FORWARDED_FOR"];

            if (isset($_SERVER["HTTP_CLIENT_IP"]))
                return $_SERVER["HTTP_CLIENT_IP"];

                return $_SERVER["REMOTE_ADDR"];
        }

            if (getenv('HTTP_X_FORWARDED_FOR'))
                return getenv('HTTP_X_FORWARDED_FOR');

            if (getenv('HTTP_CLIENT_IP'))
                return getenv('HTTP_CLIENT_IP');

                return getenv('REMOTE_ADDR');
    }
    
    
    public function savepageclick($pageid=0) {  
            if (preg_match("([^0-9])", $pageid) >0) {
                //illegal char found.
                $pageid = 1;
            }           

            if($pageid){
                
                $ipaddress    = Mage::helper('pageclicks')->getuserip();
                $currentdate  = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                
                $website_id = Mage::app()->getStore()->getWebsiteId();
                
                $insertQuery = "INSERT INTO page_clicks SET website_id = ".$website_id.",
                            link_id = ".$pageid.",
                            ipaddress = '".$ipaddress."',
                            datetime  = '".$currentdate."'
                            ";

                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $write->query($insertQuery); 
            }
            
        }
}