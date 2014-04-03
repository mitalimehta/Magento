<?php
    
    
    class Ebulb_Pageclicks_SavelinkController extends Mage_Adminhtml_Controller_action
    {
        public function saveAction() {
            $redirectlink = $this->getRequest()->getParam('link');  
            $linkid       = $this->getRequest()->getParam('id');  
            
            if (preg_match("([^0-9])", $linkid) >0) {
                //illegal char found.
                $linkid = 1;
            }

            $ipaddress    = Mage::helper('pageclicks')->getuserip();
            $currentdate  = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            
            $insertQuery = "INSERT INTO page_clicks SET website_id = 1,
                            link_id = ".$linkid.",
                            ipaddress = '".$ipaddress."',
                            datetime  = '".$currentdate."'
                            ";
           
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->query($insertQuery); 
           
            if($redirectlink)
                $this->_redirect($redirectlink);  
         
        }
    }
?>