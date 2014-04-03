<?php

class Ebulb_Purchase_OrdersController extends Mage_Adminhtml_Controller_Action
{
    protected function _initOrder($idFieldName = 'po_order_id')
    {
        $this->_title($this->__('Order'))->_title($this->__('Receipts'));

        $OrderId = $this->getRequest()->getParam($idFieldName);
        $order = Mage::getModel('purchase/order');

        if ($OrderId) {
            $order->load($OrderId);
        }

        Mage::register('current_order', $order);
        return $this;
    } 

    public function indexAction()
    {

    }
    
    /**
     * Purchase Order List
     *
     */
    public function listAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
        
    /**
     * Edit purchase order. 
     *
     */
    public function editAction()
    {

        $this->loadLayout();
        $OrderId = $this->getRequest()->getParam('po_order_id');
        Mage::register('purchase_order_id', $OrderId);
        $this->renderLayout();
    }
            
    /**
     * create new puchase order. 
     *
     */
    public function newAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * 
     *
     */
    public function createAction()
    {
        $vendor_id = $this->getRequest()->getParam('vendor_id');

        $order = Mage::getModel('purchase/order')
            ->initNewOrder($vendor_id) ;

        $this->_redirect('purchase/orders/edit/po_order_id/'.$order->getOrderId());
    }
    
    /**
     * Cancel a purchase order.
     *
     */
    public function cancelAction()
    {
        
        $po_order_id = $this->getRequest()->getParam('po_order_id');
        
        Mage::dispatchEvent('purchase_order_stockmovement_cancel_po', array('po_order_id'=>$po_order_id)); 
        
        //move to 'purchase_order_stockmovement_cancel' event to handle.
        /*$collection = mage::getModel('Purchase/StockMovement')
            ->getCollection()
            ->addFieldToFilter('sm_po_num', $po_num);
        foreach ($collection as $item)
        {
            $item->delete();
        }
        */
        /*
        $order = mage::getModel('Purchase/Order')->load($po_num);
        foreach ($order->getProducts() as $item)
        {
            $productId = $item->getpop_product_id();
            Mage::dispatchEvent('purchase_update_supply_needs_for_product', array('product_id'=>$productId));
        }
        */
        
        //Move to order cancel funciton.
        /*$collection = mage::getModel('purchase/orderitem')
            ->getCollection()
            ->addFieldToFilter('pop_order_num', $po_num);
        foreach ($collection as $item)
        {
            $item->delete();
        } */
        
        
        $purchaseOrder = Mage::getModel('purchase/order')->load($po_order_id);

        $purchaseOrder->cancel();
        
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Purchase order successfully Canceled'));
                
        $this->_redirect('purchase/orders/list');
    }
    
    /**
     * save the purchase order.
     *
     */
    public function saveAction()
    {
        $tab = '';
        
        
        $pPoOrderId = $this->getRequest()->getPost('po_order_id');
       
        //get input parametre from the webpage.
        $order = Mage::getModel('purchase/order')->load($pPoOrderId);

        $order->setPurchaseRep(Mage::getSingleton('admin/session')->getUser()->getUsername());
        
        if ($this->getRequest()->getPost('order_eta')){
            $order->setOrderEta($this->getRequest()->getPost('order_eta'));
        }else{
            $order->setOrderEta(null);
        }
        $order->setPaymentTerms($this->getRequest()->getPost('payment_term_id'));
        $order->setShippingMethod($this->getRequest()->getPost('shipping_method_id'));
        
        $order->setComments($this->getRequest()->getPost('comments'));
        $order->setStoreId($this->getRequest()->getPost('store_id'));
            
        $order->setShippingName($this->getRequest()->getPost('shipping_name'));
        $order->setShippingCompany($this->getRequest()->getPost('shipping_company'));
        $order->setShippingStreet1($this->getRequest()->getPost('shipping_street1'));
        $order->setShippingStreet2($this->getRequest()->getPost('shipping_street2'));
        $order->setShippingCity($this->getRequest()->getPost('shipping_city'));
        $order->setShippingState($this->getRequest()->getPost('shipping_state'));
        $order->setShippingZipcode($this->getRequest()->getPost('shipping_zipcode'));
        $order->setShippingCountry($this->getRequest()->getPost('shipping_country'));
        $order->setShippingTelephone1($this->getRequest()->getPost('shipping_telephone1'));
        $order->setShippingTelephone2($this->getRequest()->getPost('shipping_telephone2'));
        $order->setShippingFax($this->getRequest()->getPost('shipping_fax'));
        $order->setShippingEmail($this->getRequest()->getPost('shipping_email'));
        $order->setShippingType($this->getRequest()->getPost('shipping_type'));   
//        $order->setShippingSalesOrderId("");   
        if($this->getRequest()->getPost('po_shipping_type')=="so")
           $order->setShippingSalesOrderId($this->getRequest()->getPost('po_shipping_so_num'));       
        else
           $order->setShippingSalesOrderId("");   
        
        $order->save();                                               
        
        $purchaseOrderId = $order->getId();
 
                         
        //Process information from "review product" tab page
        foreach ($order->getOrderItems() as $item)
        {

            $itemId = $item->getId();
            
            if ($this->getRequest()->getPost('delete_'.$itemId) == 1)
            {
                $vOldQty = $item->getProductQty();
                $vProductQty = 0;
                $item->delete();
            }
            else 
            {
                $receivedQty =$item->getQtyReceipted();            
                $vOldQty = $item->getProductQty();
                $vProductQty = $this->getRequest()->getPost('product_qty_'.$itemId);

                if (!(($vProductQty < $receivedQty) || ($vOldQty < $receivedQty))){ 
                    $item->setPurchaseOrderId($purchaseOrderId);
                    $item->setProductId($this->getRequest()->getPost('product_id_'.$itemId));
                    $item->setProductName($this->getRequest()->getPost('product_name_'.$itemId));
                    
                    //$item->setpop_supplier_ref($this->getRequest()->getPost('pop_supplier_ref_'.$item->getId()));
                    
                    $item->setProductQty($vProductQty);
                    $item->setProductPrice($this->getRequest()->getPost('product_price_'.$itemId));
                    $item->setSubtotal($item->getProductQty() * $item->getProductPrice());
                    $item->setTotal($item->getSubtotal() + $item->getTax() + $item->getAdjustFee());
                    $item->save();

                    $vendorProduct = Mage::getModel("purchase/vendorproduct")
                        ->loadByProductId($order->getVendorId() , $item->getProductId());
                    $vendorProduct->setVendorSku($this->getRequest()->getPost('vendor_sku_'.$itemId))
                        ->setUnitCost($item->getProductPrice())
                        ->save();
                }else {
                    Mage::getSingleton('adminhtml/session')->addWarning($this->__('not updated for %s: new qty[%s] less than received qty [%s]', $item->getProductName(), $vProductQty, $receivedQty)); 
                    $vProductQty =  $vOldQty;
                } 

            }                
            //process update qty when order had been send to vendor
            if ((($order->getStatus() == Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY) 
                ||($order->getStatus() == Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED))
                && ($vOldQty != $vProductQty)){
                   
                    Mage::dispatchEvent('purchase_order_refresh_poqty', 
                          array('po_order_id' => $purchaseOrderId,
                              'po_product_id' => $item->getProductId(), 
                         'po_product_qty_old' => $vOldQty,
                         'po_product_qty_new' => $vProductQty));

            }                

        }


        
        //process information from "Add product" tab page, check if we have to add products
        if ($this->getRequest()->getPost('add_product') != '')
        {
            $productsToAdd = Mage::helper('purchase')->decodeInput($this->getRequest()->getPost('add_product'));
            foreach($productsToAdd as $key => $value)
            {
                //retrieves values
                $productId = $key;
                $qty = $value['qty'];
                if ($qty == '')
                    $qty = 1;
                    
                //add product
                $order->addItems($productId, $qty);
                
                //process update qty when order had been send to vendor
                if (($order->getStatus() == Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY)
                        ||($order->getStatus() == Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED)){
                       
                        Mage::dispatchEvent('purchase_order_refresh_poqty', 
                              array('po_order_id' => $purchaseOrderId,
                                  'po_product_id' => $productId, 
                             'po_product_qty_old' => 0,
                             'po_product_qty_new' => $qty));

                }
            
            }
            
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Products added'));
            $tab = 'tab_products';
            $order->resetOrderItems();
             
        }
        
        $orderSubtotal = 0.0000;
        //$orderTotal = 0.0000;
        foreach ($order->getOrderItems() as $item)
        {
                $orderSubtotal += $item->getSubtotal();
                //$orderTotal +=  $item->getTotal();;
                //Update vendor_product's vendor sku, cost...etc,.
                Mage::dispatchEvent('purchase_order_update_vendor_product', array('po_order_item'=>$item));
        }
        $order->setSubtotal($orderSubtotal)
            ->setTotal($orderSubtotal + $order->getTax() + $order->getAdjustFee() + $order->getShippingPrice())
            ->save();
        
        //Process information from "send to vendor" tab page.
        $notifyFlag = $this->getRequest()->getPost('send_to_customer');
        if ($notifyFlag == 1)
        {
            $order->notifyVendor($this->getRequest()->getPost('email_comment'));
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Vendor notified'));

            //update Po_qty in stockitem.
            foreach ($order->getOrderItems() as $item)
            {
                Mage::dispatchEvent('purchase_order_refresh_poqty', 
                                    array('po_order_id' => $order->getId(),
                                      'po_product_id' => $item->getProductId(), 
                                      'po_product_qty_old' => 0,
                                      'po_product_qty_new' => $item->getProductQty()));
            }

        }
        
        if (($order->getStatus() == Ebulb_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY)
            ||($order->getStatus() == Ebulb_Purchase_Model_Order::STATUS_PARTIALLY_RECEIVED) ){
            Mage::dispatchEvent('purchase_order_refresh_status', 
                                    array('po_order' => $order));                                       
        }
                
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Purchase order successfully Saved'));
        
        $this->_redirect('purchase/orders/edit/po_order_id/'.$order->getOrderId().'/tab/'.$tab);
        
    }
         
    /**
     * Impression 
     *
     */
    public function printAction()
    {
        $po_order_id = $this->getRequest()->getParam('po_order_id');
        $order = Mage::getModel('purchase/order')->load($po_order_id);
            
        $obj = mage::getModel('purchase/pdf_order');
        $pdf = $obj->getPdf(array($order));
        $this->_prepareDownloadResponse(mage::helper('purchase')->__('Purchase Order #').$order->getOrderId().'.pdf', $pdf->render(), 'application/pdf');            
    }   
    
        
    /**
     * 
     *
     * @param unknown_type $fileName
     * @param unknown_type $content
     * @param unknown_type $contentType
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', strlen($content))
            ->setHeader('Content-Disposition', 'attachment; filename='.$fileName)
            ->setBody($content);
    }
    

    
    /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $productsArray
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
     */
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
    {
        return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName);
    
    }
    
    /**
     * Output specified blocks as a text list
     */
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }
    


    
    /**
     * M�thode pour la grille d'ajout de produit dans une commande fournisseur
     *
     */
    public function productSelectionGridAction()
    {      
        $po_order_id = $this->getRequest()->getParam('po_order_id');
        $gridBlock = $this->getLayout()->createBlock('purchase/order_edit_tabs_productSelection')
            ->setOrderId($po_order_id)
            ->setGridUrl($this->getUrl('*/*/productSelectionGridOnly', array('_current' => true, 'po_order_id' => $po_order_id)));
        $serializerBlock = $this->_createSerializerBlock('add_product', $gridBlock, $gridBlock->getSelectedProducts());

        $this->_outputBlocks($gridBlock, $serializerBlock);
    }
    
    
  
    
    public function productSelectionGridOnlyAction()
    {
        $po_order_id = $this->getRequest()->getParam('po_order_id');
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('purchase/order_edit_tabs_productSelection')->setOrderId($po_order_id)->toHtml()
            );
    }        
        
    

 
    
    public function getShippingAddressAction()
    {
        
        $type         = (string) $this->getRequest()->getParam('po_shipping_type');
        $id           = (int)    $this->getRequest()->getParam('po_shipping_id');
        $so_num       = $this->getRequest()->getParam('po_so_num');
        $po_num       = (int)    $this->getRequest()->getParam('po_num');
        $var["error"] = true;
        
        if($type=="default1" || $type=="default2"){
           if($type=="default1")
              $key = "purchase/shipping_information1/";
           else
              $key = "purchase/shipping_information2/";   
           
           $var["name"]       = Mage::getStoreConfig($key.'name');
           $var["company"]    = Mage::getStoreConfig($key.'company');
           $var["street"]     = Mage::getStoreConfig($key.'street');
           $var["street2"]     = Mage::getStoreConfig($key.'street2');
           $var["city"]       = Mage::getStoreConfig($key.'city');
           $var["region"]       = Mage::getStoreConfig($key.'region');
           $var["postcode"]   = Mage::getStoreConfig($key.'postcode');
           $var["country"]    = Mage::getStoreConfig($key.'country');
           $var["telephone1"] = Mage::getStoreConfig($key.'telephone1');
           $var["telephone2"] = Mage::getStoreConfig($key.'telephone2');
           $var["fax"]        = Mage::getStoreConfig($key.'fax');
           $var["email"]      = Mage::getStoreConfig($key.'email');
           $var["error"] = false;
        }
        else if($type=="so" && $so_num){
           $so = Mage::getModel('sales/order')->loadByIncrementId($so_num);

           if($so->getId()){
              $address           = $so->getShippingAddress()->getData();
              //$customer          = Mage::getModel("customer/customer")->load($so->getBillingAddress()->getCustomerId())->getData();
              $var["name"]       = $address["firstname"]." ".$address["lastname"];
              $var["company"]    = $address["company"];
              $var["street"]     = $address["street"];
              $var["street2"]    = '';//$address["street2"];
              $var["city"]       = $address["city"];
              $var["region"]     = $address["region"];
              $var["postcode"]   = $address["postcode"];
              $var["country"]    = $address["country_id"];
              $var["telephone1"] = $address["telephone"];
              $var["telephone2"] = $address["telephone2"];
              $var["fax"]        = $address["fax"];
              $var["email"]      = $so->getCustomerEmail();
              $var["error"]      = false;
           }else{
              $var["error"]      = true;
              $var["message"]    = "SO";
           }
        }
        else if($type=="other" && $po_num){
              $order = Mage::getModel('purchase/order')->load($po_num);
              $var["name"]       = $order->getShippingName();
              $var["company"]    = $order->getShippingCompany();
              $var["street"]     = $order->getShippingStreet();
              $var["street2"]    = $order->getPShippingStreet2();
              $var["city"]       = $order->getShippingCity();
              $var["region"]     = $order->getShippingRegion();
              $var["postcode"]   = $order->getShippingPostcode();
              $var["country"]    = $order->getShippingCountry();
              $var["telephone1"] = $order->getShippingTelephone1();
              $var["telephone2"] = $order->getShippingTelephone2();
              $var["fax"]        = $order->getShippingFax();
              $var["email"]      = $order->getShippingEmail();
              $var["error"]      = false;
        }
            
        $this->getResponse()->setBody(Zend_Json::encode($var));    
    }

    public function checkOrderIdAction()
    {
             
        $so_num       = (int)    $this->getRequest()->getParam('po_so_num');
        $response["error"] = ""; 
        
        if(strlen($so_num) == 9){
           $so = Mage::getModel('sales/order')->loadByIncrementId($so_num);
           if($so->getId())
              $response["error"] = "false"; 
           else
              $response["error"] = "true"; 
        }
        else if(strlen($so_num) > 9){
           $response["error"] = "true"; 
        }
        else if(strlen($so_num) < 9)
           $response["error"] = ""; 
        
        $this->getResponse()->setBody(Zend_Json::encode($response));       
    }

    public function receiptsGridAction()
    {
        //$this->_initOrder('po_order_id');
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/order_edit_tabs_receipts')->setOrderId($this->getRequest()->getParam('po_order_id'))->toHtml());
    } 
 
}