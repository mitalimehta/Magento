<?php

class Ebulb_Purchase_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function log($message)
    {
        Mage::log('=== Purchase ==='.$message);
    }
    
    public function decodeInput($encoded)
    {
        parse_str($encoded, $data);
        foreach($data as $key=>$value) {
            parse_str(base64_decode($value), $data[$key]);
        }

        return $data;
    }
        
}
?>