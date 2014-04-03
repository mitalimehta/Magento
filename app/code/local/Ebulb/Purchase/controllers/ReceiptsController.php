<?php

class Ebulb_Purchase_ReceiptsController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {

    }
    
    /**
     * Receipt List
     *
     */
    public function listAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
        
    /**
     * Edit receipt order. 
     *
     */
    public function editAction()
    {

        $this->loadLayout();
        $ReceiptId = $this->getRequest()->getParam('po_receipt_id');
        Mage::register('purchase_receipt_id', $ReceiptId);
        $this->renderLayout();
    }

    /**
     * view receipt order. 
     *
     */
    public function viewAction()
    {
        $this->loadLayout();
        $orderId = $this->getRequest()->getParam('po_order_id');
        $receiptId = $this->getRequest()->getParam('po_receipt_id');
        Mage::register('purchase_order_id', $orderId);
        Mage::register('purchase_receipt_id', $receiptId);
        $this->renderLayout();
    }
    
                
    /**
     * create new receipt order. 
     *
     */
    public function newAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function createAction()
    {
        $pPoOrderId = $this->getRequest()->getPost('po_order_id');
        $pOrder = Mage::getModel('purchase/order')->load($pPoOrderId);
        
        $packageNumber = $this->getRequest()->getPost('receipt_packagenumber');
        if ($packageNumber){
            $receipt = Mage::getModel('purchase/receipt')
                ->initNewReceipt($pPoOrderId) ;

            $this->_updatePackageNumber($receipt, $this->getRequest()->getPost('receipt_packagenumber'));

            //update invoice.
            $this->_updateInvoice($receipt, 
                                 $this->getRequest()->getPost('purchase_invoice_id'), 
                                 $this->getRequest()->getPost('purchase_invoice_number'), 
                                 $this->getRequest()->getPost('purchase_invoice_shipping_fee'), 
                                 $this->getRequest()->getPost('purchase_invoice_toll'), 
                                 $this->getRequest()->getPost('purchase_invoice_subtotal'), 
                                 $this->getRequest()->getPost('purchase_invoice_total')
                                 );
            
            $message = $this->_updateReceipt($receipt, $pOrder);
            if (sizeof($message)==0){

                Mage::dispatchEvent('purchase_order_refresh_fee', array('po_order_id' => $pPoOrderId));
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Create receipt for purchase order successfully.'));
                $this->_redirect('purchase/orders/edit/po_order_id/'.$pPoOrderId.'/');

            } else {
                foreach($message as $item){
                    Mage::getSingleton('adminhtml/session')->addError(implode(", ", $item));
                } 
                $this->_redirect('*/*/edit/po_receipt_id/'.$receipt->getReceiptId().'/po_order_id/'.$pPoOrderId);
            
            }
        }else{
                Mage::getSingleton('adminhtml/session')->addError($this->__('Package number cannot be null.'));
                $this->_redirect('purchase/receipts/new/po_order_id/'.$pPoOrderId.'/');        
        }
    }

    
    public function updateAction()
    {
        $pPoOrderId = $this->getRequest()->getPost('po_order_id');
        $vReceiptId = $this->getRequest()->getPost('po_receipt_id'); 
        
        $pOrder = Mage::getModel('purchase/order')->load($pPoOrderId);
         
        $receipt = Mage::getModel('purchase/receipt') -> load($vReceiptId);

        $packageNumber = $this->getRequest()->getPost('receipt_packagenumber');
        if ($packageNumber){        
            $this->_updatePackageNumber($receipt, $this->getRequest()->getPost('receipt_packagenumber'));
            
            //update invoice.
            $this->_updateInvoice($receipt, 
                                 $this->getRequest()->getPost('purchase_invoice_id'), 
                                 $this->getRequest()->getPost('purchase_invoice_number'), 
                                 $this->getRequest()->getPost('purchase_invoice_shipping_fee'), 
                                 $this->getRequest()->getPost('purchase_invoice_toll'), 
                                 $this->getRequest()->getPost('purchase_invoice_subtotal'), 
                                 $this->getRequest()->getPost('purchase_invoice_total')
                                 );
            
            $message = $this->_updateReceipt($receipt, $pOrder);
            if (sizeof($message)==0){
                
                Mage::dispatchEvent('purchase_order_refresh_fee', array('po_order_id' => $pPoOrderId));
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Create receipt for purchase order successfully.'));
                $this->_redirect('purchase/orders/edit/po_order_id/'.$pPoOrderId.'/');

            } else {
                foreach($message as $item){
                    Mage::getSingleton('adminhtml/session')->addError(implode(", ", $item));
                } 
                $this->_redirect('*/*/edit/po_receipt_id/'.$vReceiptId.'/po_order_id/'.$pPoOrderId);
            
            }
        }else{
                Mage::getSingleton('adminhtml/session')->addError($this->__('Package number cannot be null.'));
                $this->_redirect('purchase/receipts/edit/po_order_id/'.$pPoOrderId.'/po_receipt_id/'.$vReceiptId.'/');        
        }        
        
    }
    
    public function _updatePackageNumber($receipt, $inputPackageNumber){
        
        if ( $inputPackageNumber ){
            if ($inputPackageNumber != $receipt->getPackageNumber()) {
                 $receipt->setPackageNumber($inputPackageNumber)
                    ->save();
            }
        
        }    
    }
    
    
    public function _updateInvoice($receipt, $pInvoiceId, $pInvoiceNumber, $pInvoiceShippingFee, $pInvoiceAdjustFee, $pInvoiceSubtotal, $pInvoiceTotal){ 
        
        $invoice = null;
        if ($pInvoiceId != null){
            $invoice = Mage::getModel('purchase/invoice')->load($pInvoiceId);
            $invoice->setIncrementId($pInvoiceNumber)
                ->setShippingPrice($pInvoiceShippingFee)
                ->setAdjustFee($pInvoiceAdjustFee)
                ->setSubtotal($pInvoiceSubtotal) 
                ->setTotal($pInvoiceTotal)
                ->save(); 
        } else {
            $invoice = Mage::getModel('purchase/invoice');
            $invoice->setIncrementId($pInvoiceNumber)
                ->setPurchaseReceiptId($receipt->getId())
                ->setShippingPrice($pInvoiceShippingFee)
                ->setAdjustFee($pInvoiceAdjustFee) 
                ->setSubtotal($pInvoiceSubtotal) 
                ->setTotal($pInvoiceTotal)
                ->save(); 
        
        }

    }    
    
    public function _updateReceipt($receipt, $pOrder){
        $result = null;
        
        if($receipt->getId()){
                foreach ($pOrder->getOrderItems() as $item)
                {
                    $isUpdated = false; 
                    $itemId = $item->getId();
                    $vProductId = $this->getRequest()->getPost('product_id_'.$itemId);
                    $vProductQty = $this->getRequest()->getPost('product_delivered_qty_'.$itemId);
                    if (isset($vProductId) && isset($vProductQty) && ($vProductQty >=0)){
                        $receiptItem = $receipt->getReceiptItem($vProductId);
                        $vOldQty = 0;
                        if ($receiptItem){
                            $vOldQty = $receiptItem->getQty();
                            //update qty.
                            if ($vOldQty != $vProductQty){
                                $resultTemp = $receiptItem->validate($vProductQty, $vOldQty);
                                if(sizeof($resultTemp)==0){
                                    $receiptItem->setQty($vProductQty)
                                     ->save();
                                    $isUpdated = true;
                                }else{
                                    $result[] = $resultTemp;
                                }
                            }
                            
                        } else {
                            if ($vProductQty >0){
                                //add new item.
                                $receiptItem =  Mage::getModel('purchase/receiptitem');
                                $receiptItem -> setReceiptId($receipt->getId())
                                    ->setProductId($vProductId);
                                $resultTemp = $receiptItem->validate($vProductQty, 0);    
                                if(sizeof($resultTemp)==0){    
                                   $receiptItem ->setQty($vProductQty)
                                            ->save();
                                    $isUpdated = true; 
                                } else {
                                    $result[] = $resultTemp; 
                                } 
                            }
                        }
                        if ($isUpdated == true ){
                            if($vOldQty != $vProductQty){
                                      
                                Mage::dispatchEvent('purchase_order_receipt_update', 
                                    array('po_order_id' => $pOrder->getId(),
                                      'po_order_increment_id' => $pOrder->getIncrementId(), 
                                      'po_receipt_id' => $receipt->getReceiptId(),
                                      'po_receipt_increment_id' => $receipt->getIncrementId(), 
                                      'po_product_id' => $vProductId, 
                                      'po_product_qty_old' => $vOldQty,
                                      'po_product_qty_new' => $vProductQty));                                      

                                Mage::dispatchEvent('purchase_order_refresh_status', 
                                    array('po_order' => $pOrder));                                       
                                
                                Mage::dispatchEvent('purchase_order_refresh_poqty', 
                                    array('po_order_id' => $pOrder->getId(),
                                      'po_product_id' => $vProductId, 
                                      'po_product_qty_old' => 0 - $vOldQty,
                                      'po_product_qty_new' => 0 - $vProductQty));
                            }
                        }
                    }
                }    
            }
            return $result;
    }
}   
?>
