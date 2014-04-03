<?php

class Ebulb_Automaticfeed_Adminhtml_CreateController extends Mage_Adminhtml_Controller_action
{
     protected $_PATH = '/var/www/html/magento1411/export/';
    //protected $_PATH = '/var/www/html/mitali/export/';
    
    public function indexAction()
    { 
        $this->_title($this->__('Create'))->_title($this->__('Create New Feeds'));
      
        $this->loadLayout();
       
        $this->_setActiveMenu('catalog/automaticfeed');
     
        $this->_addBreadcrumb(Mage::helper('automaticfeed')->__('Create New Feeds'), Mage::helper('automaticfeed')->__('Create New Feeds'));
       
        $this->renderLayout();
    }
    
    public function createfeedAction(){
        
        $companyList = Mage::app()->getRequest()->getParam('companylist');
        $websiteList = Mage::app()->getRequest()->getParam('websitelist');
        
        $shoppingcompanyInfo = array();
        
        if(strstr($companyList,"all")){
            $companyList = '';    
        }
        if(strstr($websiteList,"all")){
            $websiteList = '';    
        }
        
        $shoppingcompanyInfo = Mage::getModel('automaticfeed/autofeedinfo')->getcompanyinfo($companyList,$websiteList);
        
        if(count($shoppingcompanyInfo) > 0){
          
            foreach($shoppingcompanyInfo as $_shoppingcompanyInfo)
            {
                ini_set("memory_limit","512M");  
                   
                $feed_id         = $_shoppingcompanyInfo['feed_id'];
                $website_id      = $_shoppingcompanyInfo['store_id'];
                
                $ftp_host        = $_shoppingcompanyInfo['ftp_host'];
                $ftp_user        = $_shoppingcompanyInfo['ftp_user'];
                $ftp_pwd         = $_shoppingcompanyInfo['ftp_pwd'];
                $directory       = $_shoppingcompanyInfo['directory'];
                
                $ftp_testmode    = $_shoppingcompanyInfo['ftp_testmode'];
                $file_name       = $_shoppingcompanyInfo['file_name'];
                $file_type       = $_shoppingcompanyInfo['file_type'];
                $log_file_name   = $_shoppingcompanyInfo['logfile_name']; 
                
                $seperator       = $_shoppingcompanyInfo['seperator'];
                $include_header  = $_shoppingcompanyInfo['include_header'];
                
                if($seperator == 'comma'){
                    $text_seperator = ",";
                }
                else if($seperator == 'tab'){
                    $text_seperator = "\t";    
                } 
                
                $full_file_name  = $this->_PATH.$file_name;
                
                $storeIds = Mage::getModel('core/website')->load($website_id)
                            ->getStoreIds(); 
                
                foreach($storeIds as $key=>$val){
                    $store_id = $val;    
                }
               
                $productsCollection = $this->getAllProducts($store_id);  
                
                $attributesInfo  = Mage::getModel('automaticfeed/autofeedinfo')
                               ->getAttributes($feed_id);
                
                $totalattributes = count($attributesInfo);
                $counter = 1; 
                $i=0; 
               
                 if($totalattributes > 0)
                 {    
                     $list = '';  
                     foreach($attributesInfo as $_attributesInfo){ 
                        
                        if($_attributesInfo['attribute_id'] != 0)
                        {
                            $attributeObject    =  Mage::getModel('eav/entity_attribute')
                                               ->load($_attributesInfo['attribute_id']); 
                                                                                        
                            $attribute_code    =  $attributeObject->getData('attribute_code');                   
                            $attributesInfo[$i]['attribute_code'] = $attribute_code;
                            $attributesInfo[$i]['frontend_input'] = $attributeObject->getData('frontend_input');                   
                            
                            $productsCollection->addAttributeToSelect($attribute_code);
                            
                        }   
                        
                        if($list == '')
                            $list .= $_attributesInfo['company_attr'];    
                        else
                            $list .=  $text_seperator.$_attributesInfo['company_attr'];  
                       
                       if($totalattributes == $counter)
                            $list .=  " \n"; 
                            
                       $i++;
                       $counter++;  
                    }
                    
                    if($include_header == 1){  
                        file_put_contents($full_file_name, $list);  
                    }     
                    
                    $productCounter = 0;
                    foreach($productsCollection as $product) 
                    {  
                            $list = "";     
                            set_time_limit (80000);  
                            
                            $totalattributes = count($attributesInfo);
                            $counter = 1;               
                            foreach($attributesInfo as $_attributesInfo){ 
                                
                            
                                $productattr = '';
                                if($_attributesInfo['attribute_id'] == 0)
                                {
                                    if($_attributesInfo['custom_value'] == "product_category")
                                    {
                                        foreach($product->getCategoryIds() as $_categoryId)
                                        {
                                             if(!isset($cache_cat[$_categoryId]))
                                                $cache_cat[$_categoryId] = Mage::getModel('catalog/category')->load($_categoryId)->getName();
                                                
                                            $productattr = $this->remove_special_chars($cache_cat[$_categoryId]);   
                                            break;
                                        }        
                                    }
                                    else{
                                        $productattr = $this->remove_special_chars($_attributesInfo['custom_value']);
                                    }
                                }
                                else
                                {
                                    if($_attributesInfo['attribute_code'] == 'free_shipping'){
                                       
                                        if($product->getAttributeText('free_shipping') == 'yes')
                                        {  
                                            $productattr = "FREE SHIPPING";
                                             if((float)$product->getFreeShippingOver())
                                             {
                                                $productattr .= " on ORDERS OVER " .Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().round($product->getFreeShippingOver());
                                             }
                                             else
                                             {
                                                 $productattr .= "";
                                             } 
                                        }   
                                
                                    }
                                    else if($_attributesInfo['attribute_code'] == 'url_key')
                                    {
                                        $productattr = Mage::app()->getStore($store_id)->getBaseUrl() . $product->getUrlPath();  
                                    }
                                    else if($_attributesInfo['attribute_code'] == 'image')
                                    {
                                        $productattr = Mage::app()->getStore($store_id)->getBaseUrl().'media/catalog/product'.$product->getImage();
                                    }
                                    else if($_attributesInfo['attribute_code'] == 'thumbnail')
                                    {
                                        $productattr = Mage::app()->getStore($store_id)->getBaseUrl().'media/catalog/product'.$product->getThumbnail();
                                    }
                                    else if($_attributesInfo['frontend_input'] == 'select')
                                    {
                                        $productattr = $this->remove_special_chars($product->getResource()->getAttribute($_attributesInfo['attribute_code'])->getFrontend()->getValue($product));
                                    }
                                    else
                                    {
                                        $productattr = $this->remove_special_chars($product->getData($_attributesInfo['attribute_code']));    
                                    }
                                }
                                
                                if(is_numeric($productattr)){ 
                                    $productattr = round($productattr,2);    
                                }
                            
                               // if($list == '')
                                //    $list .= $productattr;    
                                //else
                                 //   $list .=  $productattr.$text_seperator;
                                
                                if($totalattributes == $counter)
                                    $list .=  $productattr." \n"; 
                                else
                                    $list .=  $productattr.$text_seperator;
                                    
                                //echo $productattr."----";
                                $counter++;    
                            }
                            //exit;
                            if($include_header == 1){   
                                file_put_contents($full_file_name, $list, FILE_APPEND);
                            }
                            else{
                                if($productCounter == 1)
                                    file_put_contents($full_file_name, $list);    
                                else
                                    file_put_contents($full_file_name, $list, FILE_APPEND);    
                                
                            }
                            
                            $productCounter++;
                            
                            flush(); 
                    }
                   
                    if($ftp_testmode == 0){  
                        //$result = '';  
                        $result = $this->save_ftp_file($ftp_host,$ftp_user,$ftp_pwd,$full_file_name,$directory); 
                        $log_handle = fopen($log_file_name, 'w'); 
                        fwrite($log_handle, $result);
                        fclose($log_handle);
                    }
                    
                 }
                 else{
                     echo "please specify attribute information in settings <br />";
                 }
            }
        
            echo "Feeds Created Successfully <br />";
        }
        else{
            echo "No records found";
        }
        exit;
    }
    
    public function getAllProducts($store_id=1){
        
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('status', 1) // enabled
            ->addAttributeToFilter('visibility', 4) // catalog, search
            ->addAttributeToFilter('type_id','simple') 
            ->addAttributeToSelect('url_path')    
            ->addAttributeToSelect('free_shipping_over')    
            ->addAttributeToSelect('free_shipping')  
            ->addStoreFilter($store_id) 
            ; 
            
        return $products;
    }
    
    function save_ftp_file($host,$user,$pass,$file_name,$dir_name="")
    {
        $fp = fopen($file_name, 'r');
        //  $conn_id = ftp_connect("ftp://ebulb:3pu35pmo9j/datafeeds.jellyfish.com") or die("Couldn't connect to $host --- jellyfish");
        $conn_id = ftp_connect($host) or die("Couldn't connect to $host"); 

        $login_result = ftp_login($conn_id, $user, $pass); 
        if (!$login_result)
            return "Cannot login. Check user name or password\n";
        ftp_pasv($conn_id, true);     
        if ($dir_name)
            if (!ftp_chdir($conn_id,$dir_name))
                return "Cannot change to directory $dir_name. Check if directory exists.\n"; 
        $file_name=basename($file_name); 
        if (!ftp_fput($conn_id,$file_name,$fp,FTP_ASCII))   
            return "There was a problem while uploading $file_name. Check why file cant be uploaded.\n"; 
        ftp_close($conn_id);
        fclose($fp);
        return "Uploaded " . $file_name . " on ". date("F j, Y, g:i a") ."\n";
    } 
    
    function remove_special_chars($string)
    {
        $string = ereg_replace(",", "", $string);
        $string = preg_replace("/\n|\r/", " ", $string);
        $string = ereg_replace("\n", "", $string); // & Symbol
        $string = ereg_replace("& ", "&amp;", $string); // & Symbol
       // $string = ereg_replace(9, " ", $string); // Tab Symbol 
        $string = ereg_replace("\t", " ", $string); // Tab Symbol 
        $string = htmlspecialchars($string); // & Symbol
        return $string;
    }
}