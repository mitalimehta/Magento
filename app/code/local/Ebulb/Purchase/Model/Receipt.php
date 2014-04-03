<?php

class Ebulb_Purchase_Model_Receipt  extends Mage_Core_Model_Abstract
{
    private $_receiptItems = null;
    private $_order = null;
        
    /*****************************************************************************************************************************
    * ***************************************************************************************************************************
    * Constructeur
    *
    */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/receipt');
    }
    
    public function getNextIncrementId(){
        $entity_type_model=Mage::getSingleton('eav/config')->getEntityType('shipment');
        $new_incr_id = $entity_type_model->fetchPONewIncrementId('1');     
        return $new_incr_id;
    }
        
    public function initNewReceipt($poId){              
        $newReceipt = Mage::getModel('purchase/receipt');

        try{
            $newReceipt->setPurchaseOrderId($poId)
                ->setIncrementId('PR5'.$this->getNextIncrementId())
                ->setPurchaseOrderId($poId)
                ->setPackageNumber('')
                ->save();

             
        }catch(Exception $e){
            Mage::logException($e);
        }

        return $newReceipt;
    }
    
    public function getReceiptItem($productId){
        
        foreach ($this->getReceiptItems() as $receiptItem)
        {
            if ($receiptItem->getProductId() == $productId)
                return $receiptItem;
        }
        return null;
    
    }
    
    public function getReceiptItems()
    {
        if ($this->_receiptItems == null)
        {
            $this->_receiptItems = Mage::getResourceModel('purchase/receiptitem_collection')
                ->addFieldToFilter('receipt_id', $this->getId());
        }
                    
        return $this->_receiptItems;
    }
    
    public function getOrder()
    {
        if ($this->_order == null)
        {
            $this->_order = Mage::getModel('purchase/order')->load($this->getPurchaseOrderId());
        }
                    
        return $this->_order;
    }
    
      
}