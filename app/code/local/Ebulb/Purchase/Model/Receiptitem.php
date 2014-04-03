<?php

class Ebulb_Purchase_Model_Receiptitem  extends Mage_Core_Model_Abstract
{
    protected $_receipt = null;
    protected $_orderitem = null;
       
    /*****************************************************************************************************************************
    * ***************************************************************************************************************************
    * Constructeur
    *
    */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/receiptitem');
    }
    
    public function getReceipt()
    {
        if (is_null($this->_receipt)) {
            
            $this->_receipt = Mage::getModel('purchase/receipt')
                                ->load($this->getReceiptId());
        } 
        return $this->_receipt;
    }

    public function setOrderItem(Ebulb_Purchase_Model_Orderitem $orderitem)
    {
        $this->_orderitem = $orderitem;
        return $this;
    }

    public function getOrderitem()
    {
        if (is_null($this->_orderitem)) {
            $this->_orderitem = $this->getReceipt()->getOrder()->getPurchaseOrderItem($this->getProductId());

        }
        return $this->_orderitem;
    }

    
    public function validate($qty, $oldQty = 0)
    {
        $result = null;
        /**
         * Check qty availability
        */
        $notReceived = $this->getOrderitem()->getProductQty() - $this->getOrderitem()->getQtyReceipted() + $oldQty;
        if (($qty <= $notReceived) && ($notReceived >= 0)){
            
        }
        else {
            
            $result[] = Mage::helper('purchase')->__('Invalid qty[%s] to receipt for item "%s"', ''.$qty, $this->getOrderitem()->getProductName());
            
        }
       
        return $result;
    }
   
}