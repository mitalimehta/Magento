<?php

class Ebulb_Purchase_Model_Invoice  extends Mage_Core_Model_Abstract
{
    private $_receipt = null;

        
    /*****************************************************************************************************************************
    * ***************************************************************************************************************************
    * Constructeur
    *
    */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/invoice');
    }
    
    public function getNextIncrementId(){
        $entity_type_model=Mage::getSingleton('eav/config')->getEntityType('invoice');
        $new_incr_id = $entity_type_model->fetchPONewIncrementId('1');     
        return $new_incr_id;
    }
 
    
    public function getReceipt()
    {
        if ($this->_receipt == null)
        {
            $this->_receipt = Mage::getModel('purchase/receipt')->load($this->getPurchaseReceiptId());
        }
                    
        return $this->_receipt;
    }
    

    
    public function loadByReceiptId($receiptId)
    {
        $invoices = Mage::getResourceModel('purchase/invoice_collection')
            ->addFieldToFilter('purchase_receipt_id', $receiptId)
            ;
                
        foreach ($invoices as $invoiceItem)
        {
            // return first one.
            return $invoiceItem;
        }                    
        return null;
    }    
      
}