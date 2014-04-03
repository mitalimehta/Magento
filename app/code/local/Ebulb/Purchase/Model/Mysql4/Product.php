<?php

class Ebulb_Purchase_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    { 
        $this->_init('purchase/product', 'vendor_product_id');
    }
    
    
    public function checkexistingproduct($productid,$vendorid,$vendor_product_id=null){   
      
       $select = $this->_getReadAdapter()->select()
            ->from(array('product'=>'po_vendor_product'), array('vendor_product_id'=>'product.vendor_product_id'))
            ->where('product.vendor_id = '.$vendorid)
            ->where('product.product_id = '.$productid)
            ;
      
       if($vendor_product_id)
            $select->where('product.vendor_product_id != '.$vendor_product_id);
       
       $vendors = $this->_getReadAdapter()->fetchAll($select);
        
        if(count($vendors)>0)
            return false;
            
        else
            return true;
    }
    
} 