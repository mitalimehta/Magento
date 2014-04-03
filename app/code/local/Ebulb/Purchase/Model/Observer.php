<?php

class Ebulb_Purchase_Model_Observer 
{

    public function processOrderRefreshFeeEvent(Varien_Event_Observer $observer){
/*
                Mage::dispatchEvent('purchase_order_refresh_fee', array('po_order_id' => $pPoOrderId));
*/
        try{
            $poOrderId = $observer->getEvent()->getPoOrderId();
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql  = "update po_order as poo, (
                        select sum(poi.shipping_price ) as shipping_price, sum(poi.adjust_fee ) as adjust_fee
                        from po_receipt as por,
                             po_invoice as poi 
                        where por.purchase_order_id = ? 
                        and   poi.purchase_receipt_id = por.receipt_id
                        )  as tempt 
                        set poo.shipping_price = tempt.shipping_price , 
                        poo.adjust_fee = tempt.adjust_fee,
                        poo.total = poo.subtotal + tempt.shipping_price + tempt.adjust_fee
                        where poo.order_id = ?
                        ";
            $result= $write->query($sql, array($poOrderId, $poOrderId));
        }catch (Exception $ex)
        {
            Mage::logException($ex);
        }    
    }

    public function processOrderRefreshPoqtyEvent(Varien_Event_Observer $observer){
/*
                                Mage::dispatchEvent('purchase_order_refresh_poqty', 
                                    array('po_order_id' => $pOrder->getId(),
                                      'po_product_id' => $vProductId, 
                                      'po_product_qty_old' => 0 - $vOldQty,
                                      'po_product_qty_new' => 0 - $vProductQty));
*/
        try{
            $poOrderId = $observer->getEvent()->getPoOrderId();
            $poProductId = $observer->getEvent()->getPoProductId();
            $poProductQtyOld = $observer->getEvent()->getPoProductQtyOld();
            $poProductQtyNew = $observer->getEvent()->getPoProductQtyNew();
            
            $productQty = $poProductQtyNew - $poProductQtyOld;
            if ($productQty != 0){
                $vStockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($poProductId);
                if ($vStockItem) {
                    $vStockItem->setPoQty($vStockItem->getPoQty() + $productQty)->save(); 
                }
            }
        }catch (Exception $ex)
        {
            Mage::logException($ex);
        }    
    }    
    
    public function processReceiptUpdateEvent(Varien_Event_Observer $observer)
    {
        /*
                                Mage::dispatchEvent('purchase_order_receipt_update', 
                                    array('po_order_id' => $pOrder->getId(),
                                      'po_order_increment_id' => $pOrder->getIncrementId(), 
                                      'po_receipt_id' => $receipt->getReceiptId(),
                                      'po_receipt_increment_id' => $receipt->getIncrementId(), 
                                      'po_product_id' => $vProductId, 
                                      'po_product_qty_old' => $vOldQty,
                                      'po_product_qty_new' => $vProductQty));
        */
                               
        try{
            $poOrderId = $observer->getEvent()->getPoOrderId();
            $poOrderIncrementId = $observer->getEvent()->getPoOrderIncrementId();
            $poReceiptId = $observer->getEvent()->getPoReceiptId();
            $poReceiptIncrementId = $observer->getEvent()->getPoReceiptIncrementId();
            $poProductId = $observer->getEvent()->getPoProductId();
            $poProductQtyOld = $observer->getEvent()->getPoProductQtyOld();
            $poProductQtyNew = $observer->getEvent()->getPoProductQtyNew();
            
            $productQty = $poProductQtyNew - $poProductQtyOld;
        
            if ($productQty != 0){

                $vNewStockmovement = Mage::getModel('purchase/stockmovement');
                $vNewStockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($poProductId);;
        
                $comments = $vNewStockmovement->getStockmovementType('purchaseorder');
                $comments .= Mage::helper('purchase')->__(' by %s', Mage::getSingleton('admin/session')->getUser()->getUsername());
                if ($poProductQtyOld != 0){
                    $comments .= Mage::helper('purchase')->__(' :changed from %s to %s', $poProductQtyOld, $poProductQtyNew);
                }
                $vNewStockmovement->setDocId($poOrderIncrementId.'/'.$poReceiptIncrementId)
                    ->setProductId($poProductId)
                    ->setProductQty($productQty)
                    ->setComments($comments )
                    ->save();
                    
                $vNewStockItem->setQty( $vNewStockItem->getQty() + $productQty)
                    ->setTotalQty( $vNewStockItem->getTotalQty() + $productQty)
                    ->save();                
                
            }
        
          
        }catch (Exception $ex)
        {
            Mage::logException($ex);
        }
    }
 
    public function processOrderRefreshStatusEvent(Varien_Event_Observer $observer)
    {
        /*
                                Mage::dispatchEvent('purchase_order_refresh_status', 
                                    array('po_order_id' => $pOrder->getId()));  
        */
                               
        try{
            $poOrder = $observer->getEvent()->getPoOrder();
            $poOrder->refreshStatus();
        
          
        }catch (Exception $ex)
        {
            Mage::logException($ex);
        }
    }
    
         
    public function newPurchaseOrderNotifyCustomerEvent(Varien_Event_Observer $observer)
    {
       
        try{
            $order = $observer->getEvent()->getOrder();
                    /*
        $Notify = $this->getRequest()->getPost('send_to_customer');
        if ($Notify == 1)
        {
            $order->notifySupplier($this->getRequest()->getPost('email_comment'));
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Supplier notified'));
            if ($this->getRequest()->getPost('ch_change_order_status_to_pending') == "1")
                $order->setpo_status(MDN_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY)->save();
        }
        */
        
        }catch (Exception $ex)
        {
            Mage::logException($ex);
        }
    }
 
}
