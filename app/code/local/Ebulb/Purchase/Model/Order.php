<?php

class Ebulb_Purchase_Model_Order  extends Mage_Core_Model_Abstract
{
	private $_orderItems = null;
	private $_currency = null;
	
	//Purchase order statuses
	const STATUS_NEW = 'New';
	const STATUS_INQUIRY = 'Inquiry';
	const STATUS_WAITING_FOR_DELIVERY = 'Waiting_for_delivery';
	const STATUS_PARTIALLY_RECEIVED = 'Partially_delivery';
	const STATUS_COMPLETE = 'Complete';
    const STATUS_CANCELED = 'Canceled';
	
	/*****************************************************************************************************************************
	* ***************************************************************************************************************************
	* Constructeur
	*
	*/
	public function _construct()
	{
		parent::_construct();
		$this->_init('purchase/order');
	}
    
    public function initNewOrder($vendorId){              

        $newOrder = Mage::getModel('purchase/order');

        try{
            $newOrder->setVendorId($vendorId)
                ->setIncrementId('PO'.$this->getNextIncrementId())
                ->setOrderEta(null)
                ->setStatus(Ebulb_Purchase_Model_Order::STATUS_NEW)
                ->save();
        }catch(Exception $e){
            Mage::logException($e);
        }

        return $newOrder;
    }
    
    public function getNextIncrementId(){
        $entity_type_model=Mage::getSingleton('eav/config')->getEntityType('order');
        $new_incr_id = $entity_type_model->fetchPONewIncrementId('1');     
        return $new_incr_id;
    }
	
		
	public function getOrderItems()
	{
		if ($this->_orderItems == null)
		{
			$this->_orderItems = Mage::getResourceModel('purchase/orderitem_collection')
				->addFieldToFilter('purchase_order_id', $this->getOrderId())
		       	->join('catalog/product',
			           'entity_id=main_table.product_id ') ;
        //    Mage::helper('purchase')->log( 'sql join='.$this->_orderItems->getSelect());;

         
/*     
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        
        $sql  = "SELECT `pooi`.*, povp.vendor_product_id, povp.vendor_id, povp.vendor_sku, povp.unit_cost, povp.master_carton_pack, povp.avg_cost,povp.last_addon_cost, povp.last_landed_cost, povp.lead_time
         FROM `po_order_item` as `` 
join `catalog_product_entity` as `cp` on pooi.product_id = cp.entity_id
left join  `po_vendor_product` AS `povp` on pooi.product_id = povp.product_id
where pooi.purchase_order_id =?";
        $readresult= $read->query($sql, array($this->getOrderId()));

        $this->_orderItems = $readresult->fetch();
*/          
        
        }
        			
		return $this->_orderItems;
	}
    
	public function resetOrderItems()
	{
		$this->_orderItems = null;
	}

    public function cancel()
    {
        $this->setCancelDate(date('y-m-d H:i'));
        //set order status
        $this->_updateOrderStatus(Ebulb_Purchase_Model_Order::STATUS_CANCELED);
    }
    	/**
	 * Retourne le fournisseur
	 *
	 */
	public function getVendor()
	{
		$vendor = Mage::getModel('purchase/vendor')->load($this->getVendorId());
		return $vendor;
	}
	
	/**
	 * Retourne l'objet currency li� � la commande
	 *
	 */
	public function getCurrency()
	{
		if ($this->_currency == null)
		{
			$this->_currency = Mage::getModel('directory/currency')->load($this->getpo_currency());
		}
		return $this->_currency;
	}

	    
    /**
     * Ajoute un produit a une commande
     *
     * @param unknown_type $ProductId
     * @param unknown_type $order
     */
    public function addItems($ProductId, $qty = 1)
    {
    	$purchaseOrderItem = $this->getPurchaseOrderItem($ProductId);
    	
    	//if product is not present
    	if ($purchaseOrderItem == null)
    	{
			$vendorProductModel = Mage::getModel('purchase/vendorproduct')
                ->loadByProductId($this->getVendorId(), $ProductId);
                            
		    $product = Mage::getModel('catalog/product')->load($ProductId);
            

            
            
	    	//$ref = $this->getVendor()->getProductReference($ProductId);
	    	//$supplierId = $this->getpo_sup_num();
	    	
	    	$price = 0;
	    	//if (Mage::getStoreConfig('purchase/purchase_order/auto_fill_price'))
		    //	$price = $ProductSupplierModel->getProductForSupplier($ProductId, $supplierId);
			//if (Mage::getStoreConfig('purchase/purchase_order/use_product_cost'))
		    $price = $vendorProductModel->getUnitCost();

		    	
			$orderItem = Mage::getModel('purchase/orderitem')
				->setPurchaseOrderId($this->getId())
				->setProductId($ProductId)
				->setProductName($product->getName())
				->setProductQty($qty)
				->setProductPrice($price)
				->setTaxRate($product->getPurchaseTaxRate());
                
            $orderItem->setSubtotal($orderItem->getProductQty() * $orderItem->getProductPrice())
                ->setTotal($orderItem->getSubtotal() + $orderItem->getTax() + $orderItem->getAdjustFee() )
                ->save();
    	}
    	else 
    	{
    		//if product already belong to the PO, increase qty
    		$purchaseOrderItem->setProductQty($purchaseOrderItem->getProductQty() + $qty)
              ->save();
    	}
        
        //set order status
        if ($this->isStatusNew() || $this->isStatusInquiry()){
            $this->_updateOrderStatus(Ebulb_Purchase_Model_Order::STATUS_INQUIRY);
        }
    }
    
    private function _updateOrderStatus($newStatus){
        
        $this->setStatus($newStatus);
        $this->setPurchaseRep(Mage::getSingleton('admin/session')->getUser()->getUsername());
        $this->save();
    
    }
    
    /**
     * Return a purchase order item for a product id
     *
     * @param unknown_type $productId
     */
    public function getPurchaseOrderItem($productId)
    {
    	foreach ($this->getOrderItems() as $product)
    	{                                             
    		if ($product->getProductId() == $productId)
    			return $product;
    	}
    	return null;
    }
    
    /**
     * return statuses
     *
     */
    public function getStatuses()
    {
		$retour = array();
		$retour[Ebulb_Purchase_Model_Order::STATUS_NEW] = Mage::helper('purchase')->__('New');
		$retour[Ebulb_Purchase_Model_Order::STATUS_INQUIRY] = Mage::helper('purchase')->__('Inquiry');
		$retour[Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY] = Mage::helper('purchase')->__('Waiting for delivery');
		$retour[Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED] = Mage::helper('purchase')->__('Partially recieved');
		$retour[Ebulb_Purchase_Model_Order::STATUS_COMPLETE] = Mage::helper('purchase')->__('Complete');
        $retour[Ebulb_Purchase_Model_Order::STATUS_CANCELED] = Mage::helper('purchase')->__('Canceled');
		
        
        return $retour;	
    }
    
    public function getShippingAddressAsText(){
        
        $txt   = "";
        $phone = array();
        
        $txt .= $this->getShippingName()       ? "Att. ".$this->getShippingName()."\n"          : "";
        $txt .= $this->getShippingCompany()    ? $this->getShippingCompany()."\n"               : "" ;
        $txt .= $this->getShippingStreet1()     ? $this->getShippingStreet1()."\n"                : "" ;
        $txt .= $this->getShippingStreet2()     ? $this->getShippingStreet2()."\n"                : "" ;
        $txt .= $this->getShippingCity()       ? $this->getShippingCity().", "                  : "" ;
        $txt .= $this->getShippingState()   ? $this->getShippingState()."\n"              : "" ;
        $txt .= $this->getShippingZipcode()   ? $this->getShippingZipcode()."\n"              : "" ;
        $txt .= $this->getShippingCountry()    ? $this->getShippingCountry()."\n"               : "" ;
        
        if($this->getShippingTelephone1())
           $phone[] = $this->getShippingTelephone1();
        
        if($this->getShippingTelephone2())
           $phone[] = $this->getShippingTelephone2();   
        
        $txt .= $phone ? "Phone : ".join(' \ ',$phone)."\n": "" ;
        $txt .= $this->getShippingFax()        ? "Fax : "  .$this->getShippingFax()."\n"        : "" ;
        $txt .= $this->getShippingEmail()      ? "Email : " .$this->getShippingEmail()."\n"      : "" ;
        return $txt;
        
    }

    
   /**
     * Send order per email to supplier
     *  Currently disable the function to send email directly to vendor.
     */
    public function notifyVendor($msg)
    {
        
        $this->setNotifyVendorDate(date('y-m-d H:i'))->save();
        $this->_updateOrderStatus(Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY);
        return true;
        
                
        //retrieve information
        $email = $this->getVendor()->getEmail();
        if ($email == '')
            return false;
        $cc = Mage::getStoreConfig('purchase/notify_vendor/cc_to');
        $identity = Mage::getStoreConfig('purchase/notify_vendor/email_identity');
        $emailTemplate = Mage::getStoreConfig('purchase/notify_vendor/email_template');
        
        if ($emailTemplate == '')
            die('Email template is not set (system > config > purchase)');
        
        //get pdf
        $Attachment = null;
        $pdf = Mage::getModel('purchase/pdf_order')->getPdf(array($this));
exit;
        $Attachment = array();
        $Attachment['name'] = Mage::helper('purchase')->__('Purchase Order #').$this->getOrderId().'.pdf';
        $Attachment['content'] = $pdf->render();
        
        //definies datas
        $data = array
            (
                'company_name'=>Mage::getStoreConfig('purchase/notify_vendor/company_name'),
                'message'=>$msg
            );
        
        //send email
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'adminhtml'))
            ->sendTransactional(
                $emailTemplate,
                $identity,
                $email,
                '',
                $data,
                null,
                $Attachment);
                
        //send email to cc
        if ($cc != '')
        {
            Mage::getModel('core/email_template')
                ->setDesignConfig(array('area'=>'adminhtml'))
                ->sendTransactional(
                    $emailTemplate,
                    $identity,
                    $cc,
                    '',
                    $data,
                    null,
                    $Attachment);
        }
        
        $translate->setTranslateInline(true);
        $this->setNotifyVendorDate(date('y-m-d H:i'))->save();
        
        return true;
    }
    
    public function refreshStatus(){
        $leftQty = 0;
        $totalQty = 0;
        
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql  = "select sum(tempt.leftnumber)+1 as leftqty, sum(tempt.totalnumber) as totalqty
from(SELECT  temp1.product_id, (temp1.product_qty- sum(if(isnull(pori.qty),0,pori.qty))) as leftnumber, temp1.product_qty as totalnumber
                    FROM (select  por.receipt_id, pooi.* 
                          from `po_receipt` as `por`, 
                               `po_order_item` as `pooi`
                          where por.purchase_order_id = pooi.purchase_order_id
                          and   pooi.purchase_order_id = ?
                          ) temp1   
                    left  join `po_receipt_item` as `pori` on 
                    (pori.product_id = temp1.product_id
                    and pori.receipt_id = temp1.receipt_id)
                    group by temp1.product_id, temp1.product_qty
          ) as tempt";
        $readresult= $read->query($sql, array($this->getId()));
        while ($row = $readresult->fetch() ) {
            $leftQty = $row['leftqty'];
            $totalQty = $row['totalqty'];
            break;
        }
        if($leftQty){
            if ($leftQty == 1.00){
                if (($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_COMPLETE ) 
                ||($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY)
                ||($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED)) {
                
                    $this->_updateOrderStatus(Ebulb_Purchase_Model_Order::STATUS_COMPLETE);
                }
            } else {
                if (($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_COMPLETE ) 
                ||($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY)
                ||($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED)) {
                    
                    if ($leftQty == $totalQty +1){
                        $this->_updateOrderStatus(Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY);
                    }else {
                        $this->_updateOrderStatus(Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED);
                    }
                }
            }
        }                
        
    }

    public function isStatusNew(){
        return ($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_NEW);
    }
    
    public function isStatusInquiry(){
        return ($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_INQUIRY);
    }
    
    public function isStatusWaitingForDelivery(){
        return ($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY);
    }
    
    
    public function isStatusPartiallyReceived(){
        return ($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED);
    }    
    
    public function isStatusComplete(){
        return ($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_COMPLETE);
    }                
    
    public function isStatusCanceled(){
        return ($this->getStatus() == Ebulb_Purchase_Model_Order::STATUS_CANCELED);
    } 
}