<?php

class Ebulb_Purchase_Model_Vendorproduct  extends Mage_Core_Model_Abstract
{
	

	public function _construct()
	{
		parent::_construct();
		$this->_init('purchase/vendorproduct');
	}
	
	/**
	 * Retrieve price for produt
	 *
	 */
	public function getProductFromVendor($productId, $vendorId)
	{
		$value = 0;
		
		$collection = $this->getCollection()
							->addFieldToFilter('product_id', $productId)
							->addFieldToFilter('vendor_id', $vendorId);
		
		foreach($collection as $item)
		{
			$value = $item->getUnitCost();
		}
							
		
		return $value;
	}
    
    public function updateVendorSku($productId, $vendorSku){
        
        
        
    }
	
 
    public function updateProduct($product, $vendorId){
 
        $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $product->getId())
                            ->addFieldToFilter('vendor_id', $vendorId);
                                   
        if ($collection->count() <=0){
            $this->setVendorId($vendorId);
            $this->setProductId($product->getId());
            $this->setVendorSku($product->getSku());
            $this->setUnitCost($product->getCost());
            $this->setMasterCartonPack(1);
            $this->setAvgCost($product->getCost());
            $this->setLastAddonCost($product->getCost());
            $this->setLastLandedCost($product->getCost());
            $this->setLeadTime('');
            $this->save();
//            Mage::helper('purchase')->log(' add Product ('.$product->getId(). ') in Vendors table('.$vendorId.')');
            return $this;            
        }else {
            Mage::helper('purchase')->log(' Product ('.$product->getId(). ') already existed in Vendors table('.$vendorId.')');
        }
        
    }
        
    /*
    public function getVendorSkuFromProductId($vendorId, $productId){
        $return = "";
        $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('vendor_id', $vendorId);
        foreach($collection as $item){
            $return = $item->getVendorSku();
            return $return;    
        }
        
        return $return;
        
        
    }
    
    public function setVendorSkuFromProductId($vendorId, $productId, $vendorSku){
        $return = "";
        $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('vendor_id', $vendorId);
        foreach($collection as $item){
            $item->setVendorSku($vendorSku);
            $item->save();
            
        }
        
    }
    */
    public function loadByProductId($vendorId, $productId){
        
        $return = null;
        $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('vendor_id', $vendorId);
//Mage::helper('purchase')->log(' add Product ('.$productId. ') in Vendors table('.$vendorId.')'.$collection->getSelect());                             
        foreach($collection as $item){
            return $item;
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        return $this->updateProduct($product,$vendorId);                                   
        
    }        
}