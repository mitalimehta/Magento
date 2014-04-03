<?php

class Ebulb_Pageclicks_Model_Mysql4_Pageclicks extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_productgroupTable;
    protected $_productgroupattrTable;
    protected $_productgroupattroptionsTable; 
    protected $_productgroupproductTable; 
    
    protected $_groupid;  
    protected $_totalcombinations;  
    
    protected $_criteriaChanged;
    
    public function _construct()
    {    
        // Note that the testimonial_id refers to the key field in your database table.
        $this->_init('pageclicks/pageclicks', 'entity_id');
        
        $this->_productgroupTable                = $this->getTable('productgroup/catalog_product_group');
        $this->_productgroupattrTable            = $this->getTable('productgroup/catalog_product_group_attr');
        $this->_productgroupattroptionsTable     = $this->getTable('productgroup/catalog_product_group_attr_option');
        $this->_productgroupproductTable         = $this->getTable('productgroup/catalog_product_group_product');
    } 
   
    
    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (strcmp($value, (int)$value) !== 0) {
            $field = 'identifier';
        }
        return parent::load($object, $value, $field);
    }
    
    public function savegroupattributes($productattributes,$productattributecode,$groupid){ 
       
        $this->_groupid = $groupid;
        
        //$this->deletegroupproducts($this->_groupid);
        
        $data = array();
        
        $select = $this->_getReadAdapter()->select()
                ->from(array('attributes'=>$this->_productgroupattrTable),array('attribute_id'))
                ->where('attributes.Groupid = '.$groupid);
                
        $attributes = $this->_getReadAdapter()->fetchAll($select);        
        
        $attributesData = array();  
        
        if(count($attributes) > 0){
            $counter=0;
            foreach($attributes as $attribute){
                $attributesData[$counter] = $attribute['attribute_id'];
                $counter++;   
            }
        }
       
        if(count(array_diff($attributesData,$productattributes)) != 0 || count(array_diff($productattributes,$attributesData)) != 0)
        {  
            $this->_criteriaChanged = 1;
        }  

        foreach($productattributes as $key=>$val){
             
            $data['Groupid'] = $groupid;
            $data['attribute_id'] = $val;
            $data['attribute_code'] = $productattributecode[$key];  
           
            if(!in_array($val,$attributesData)){ 
                $this->_getWriteAdapter()->insert($this->_productgroupattrTable, $data); 
            } 
        }
        
        foreach($attributesData as $attribute){
            if(!in_array($attribute,$productattributes)){ 
                $this->_getWriteAdapter()->delete($this->_productgroupattrTable, "attribute_id={$attribute} AND Groupid = {$this->_groupid}");    
                $this->_getWriteAdapter()->delete($this->_productgroupattroptionsTable, "attribute_id={$attribute} AND Groupid = {$this->_groupid}");    
            }     
        }
        
       
    }
    
    public function savegroupattributeoptions($productattributeoptions,$attrid){  
        
        $data = array();
        
        $select = $this->_getReadAdapter()->select()
                ->from(array('options'=>$this->_productgroupattroptionsTable),array('option_id'))
                ->where('options.Groupid = '.$this->_groupid)
                ->where('options.attribute_id = '.$attrid);
                
        $options = $this->_getReadAdapter()->fetchAll($select);        
        
        $optionsData = array();  
       
        if(count($options) > 0){
            $counter=0;
            foreach($options as $option){
                $optionsData[$counter] = $option['option_id'];
                $counter++;   
            }
        }
        
        /*if(count(array_diff($optionsData,$productattributeoptions)) != 0 || count(array_diff($productattributeoptions,$optionsData)) != 0 )
        {
            $this->_criteriaChanged = 1;
        }*/
        
        if(count($productattributeoptions) > 0){
            foreach($productattributeoptions as $val){
                $data['Groupid'] = $this->_groupid;
                $data['attribute_id'] = $attrid;
                $data['option_id'] = $val;
        
                if(!in_array($val,$optionsData)){ 
                    $this->_getWriteAdapter()->insert($this->_productgroupattroptionsTable, $data);    
                }
            }
        }
        
        foreach($optionsData as $option){
            if(!in_array($option,$productattributeoptions)){ 
                $this->_getWriteAdapter()->delete($this->_productgroupattroptionsTable, "attribute_id={$attrid} AND Groupid = {$this->_groupid} AND option_id = {$option}");    
            }     
        }
        
        
            
    }
    
    public function getattributesbygroupid($groupid){
        
        $select = $this->_getReadAdapter()->select()
                ->from(array('attributes'=>$this->_productgroupattrTable),array('attribute_id','attribute_code'))
                ->where('attributes.Groupid = '.$groupid);
                
        $attributes = $this->_getReadAdapter()->fetchAll($select);

       return $attributes;
    }
    
    public function getattributeoptionsbygroupid($groupid,$attributeid){
        $select = $this->_getReadAdapter()->select()
                ->from(array('options'=>$this->_productgroupattroptionsTable),array('option_id'))
                ->where('options.Groupid = '.$groupid)
                ->where('options.attribute_id = '.$attributeid);
                
        $options = $this->_getReadAdapter()->fetchAll($select);

       return $options;    
    }
    
    public function saveproducts(){
        
        $this->_totalcombinations = Mage::app()->getRequest()->getParam('totalcombinations');
        $this->deletegroupproducts($this->_groupid);  
        
        $data = array(); 
        
        if(!$this->_criteriaChanged){
            
            $product_model = Mage::getModel('catalog/product');
        
            for($i=1;$i<=$this->_totalcombinations;$i++){
                $data['Groupid'] = $this->_groupid;
                //$data['combinationid'] = $i;
            
                $productsku = Mage::app()->getRequest()->getParam('productsku_'.$i);
            
            
                try{$product = $product_model->loadByAttribute('sku',$productsku);}catch(Exception $e){
                    echo "*Invalid code:$productsku<br> ";
                    flush();
                    continue;}
                  
                  if($product) {
                    $product_id = $product->getId();    
                  }
                  else {
                    $product_id = '';
                  }
                   
                $data['product_id'] = $product_id;
                $data['option_indent'] = Mage::app()->getRequest()->getParam('option_id_'.$i);  
                
                $options = array();
                
                if($data['product_id']){
                    $select = $this->_getReadAdapter()->select()
                        ->from(array('groupproduct'=>$this->_productgroupproductTable),array('option_indent'))
                        ->where('groupproduct.Groupid = '.$data['Groupid'])
                        ->where("groupproduct.option_indent = '".$data['option_indent']."'")
                        ->where('groupproduct.product_id = '.$data['product_id']);
                    
                    $options = $this->_getReadAdapter()->fetchAll($select);  
                }
                
               if(count($options) == 0) 
                    $this->_getWriteAdapter()->insert($this->_productgroupproductTable, $data);  
                
                /*$option_id_array = array();
                $option_id_array = Mage::app()->getRequest()->getParam('option_id_'.$i);
                
                foreach($option_id_array as $key=>$val){
                    $data['option_id'] = $val;
                    $this->_getWriteAdapter()->insert($this->_productgroupproductTable, $data);
                }*/
                
                
            } 
        }
    }
    
    public function getproductskubycombination($groupid,$optionstr){
        $select = $this->_getReadAdapter()->select()
                ->from(array('groupproduct'=>$this->_productgroupproductTable),array('product_id'))
                ->where('groupproduct.Groupid = '.$groupid) 
                ->where('groupproduct.option_indent = "'.$optionstr.'"');
                       
        $product = $this->_getReadAdapter()->fetchAll($select);
        
        if($product && is_array($product) && $product[0]['product_id'] != 0){
            $product_model = Mage::getModel('catalog/product');  
            $product_id    = $product[0]['product_id'];
            
            try{$product = $product_model->load($product_id);}catch(Exception $e){
                echo "*Invalid code:$product_id<br> ";
                flush();
                continue;}
           
            if($product) { 
                 $product_sku = $product->getSku();    
            }
            else {
                 $product_sku = '';
            }
            
            return $product_sku; 
        }
        else{
            return '';
        }   
    }
    
    public function getproductidbycombination($groupid,$optionstr){ 
        $select = $this->_getReadAdapter()->select()
                ->from(array('groupproduct'=>$this->_productgroupproductTable),array('product_id'))
                ->where('groupproduct.Groupid = '.$groupid) 
                ->where('groupproduct.option_indent = "'.$optionstr.'"');
                       
        $product = $this->_getReadAdapter()->fetchAll($select);
        
        if($product && is_array($product) && $product[0]['product_id'] != 0){
            //$product_model = Mage::getModel('catalog/product');  
            $product_id    = $product[0]['product_id'];          
            return $product_id;
        }
        else{
            return '';
        }
   
    }
    
    public function getproductidstorefilter($groupid,$optionstr){ 
        $select = $this->_getReadAdapter()->select()
                ->from(array('groupproduct'=>$this->_productgroupproductTable),array('product_id'))
                ->where('groupproduct.Groupid = '.$groupid) 
                ->where('groupproduct.option_indent = "'.$optionstr.'"');
                       
        $product = $this->_getReadAdapter()->fetchAll($select);
        
        if($product && is_array($product) && $product[0]['product_id'] != 0){
            $product_model = Mage::getModel('catalog/product');  
            $product_id    = $product[0]['product_id'];
           
            //try { $product = $product_model->load($product_id)->addStoreFilter(); } catch (Exception $e) {
            try {$product = $product_model->load($product_id); } catch (Exception $e) {
                echo "*Invalid code:$product_id<br> ";
                flush();
                continue;}
           
            if($product){ 
                $websitesids = $product->getWebsiteIds(); 
                $storeid     = Mage::app()->getStore()->getId();
                
                if(in_array($storeid,$websitesids) && $product->getStatus() != 2 && $product->getVisibility() != 1)
                    $product_id = $product->getId();  
                else 
                    $product_id = ''; 
                
                //$product_id = $product->getId();     
                return $product_id;  
            }
            else {
                 return '';
            }
            
        }
        else{
            return '';
        }
   
    }
    
    public function deletegroupproducts($groupid){
        $this->_getWriteAdapter()->delete($this->_productgroupproductTable, "Groupid={$groupid}");        
    }
    
    public function getgroupid($productid){
        $select = $this->_getReadAdapter()->select()
                ->from(array('groupproduct'=>$this->_productgroupproductTable),array('Groupid','option_indent'))
                ->where('groupproduct.product_id = '.$productid); 
                       
        $group = $this->_getReadAdapter()->fetchAll($select);
        
        return $group;      
    }
}