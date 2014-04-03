<?php

class Ebulb_Purchase_Model_Orderitem  extends Mage_Core_Model_Abstract
{
	private $_currency = null;
		
	/*****************************************************************************************************************************
	* ***************************************************************************************************************************
	* Constructeur
	*
	*/
	public function _construct()
	{
		parent::_construct();
		$this->_init('purchase/orderitem');
	}
	    
    /**
     * Retourne le total pour une ligne
     *
     */
    public function getRowSubtotal()
    {
    	return round($this->getSubtotal(), 2);
    }

    /**
     * Retourne le total pour une ligne avec les taxes
     *
     */
    public function getRowTotal()
    {
        return round($this->getTotal(), 2);

    }
    
    public function getVendorSku(){
        return "";
    
    }
    
    public function setVendorSku($vendorSku){
    
        $venderProductSku = Mage::getModel("purchase/vendorproduct")->getVendorSkuFromProductId($this->getOrder()->getVendorId() , $item->getProductId() );    
        
    
    }
    
    /**
     * return statuses
     *
     */
    public function getQtyReceipted()
    {
        $totalQty = $this->getProductQty();
        $receivedQty = 0;
                
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql  = "SELECT  sum(pori.qty) as receivedqty
                    FROM `po_receipt_item` as `pori`, 
                         `po_receipt` as `por`, 
                         `po_order_item` as `pooi`
                    where pori.receipt_id = por.receipt_id
                    and   por.purchase_order_id = pooi.purchase_order_id
                    and   pori.product_id = pooi.product_id
                    and   pooi.product_id =?
                    and   pooi.purchase_order_id = ?";
        $readresult= $read->query($sql, array($this->getProductId(), $this->getPurchaseOrderId()));
        while ($row = $readresult->fetch() ) {
            return  $row['receivedqty'];
        }
                        
        return $receivedQty;    
    }
          

    
}