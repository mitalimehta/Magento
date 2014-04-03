<?php

class Ebulb_Purchase_Adminhtml_InventoryController extends Mage_Adminhtml_Controller_action
{
    public function indexAction()
    { 
        $this->_title($this->__('Inventory'))->_title($this->__('Manage Inventory'));
      
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->loadLayout();
       
        $this->_setActiveMenu('purchase/inventory');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Inventory'), Mage::helper('purchase')->__('Inventory'));
        //$this->_addBreadcrumb(Mage::helper('purchase')->__('Manage Contacts'), Mage::helper('purchase')->__('Manage Contacts'));

        $this->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_vendor_grid')->toHtml());
    }
    
    /**
     * vendor edit action
     */
     public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('catalog/product')->load($id);
        //echo "<pre>";print_r($model->getData());exit;
        if ($model->getId() || $id == 0) {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {   
                $model->setData($data);
            }

            Mage::register('current_product', $model);

            $this->loadLayout();
            $this->_setActiveMenu('purchase/vendor');

            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            //$this->_addContent($this->getLayout()->createBlock('purchase/adminhtml_inventory_edit'))
            //    ->_addLeft($this->getLayout()->createBlock('purchase/adminhtml_inventory_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('purchase')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    
    public function exportCsvAction()
    {
        $fileName   = 'inventory_summary.csv';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_inventory_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'inventory_summary.xml';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_inventory_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    public function orderAction() {
        $this->_initProduct();
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_inventory_edit_tab_vendor')->toHtml()); 
    } 
    
    public function stockmovementAction() {
        $this->_initProduct();
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_inventory_edit_tab_stockmovement')->toHtml()); 
    }
    
    public function salesorderAction() {
       
        $this->_initProduct();
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_inventory_edit_tab_salesorder')->toHtml()); 
    } 
    
    public function purchasesorderAction() {
        $this->_initProduct();
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_inventory_edit_tab_purchasesorder')->toHtml()); 
    } 
  
    protected function _initProduct($idFieldName = 'id')
    {
        $this->_title($this->__('Products'))->_title($this->__('Manage Inventory'));

        $productId = (int) $this->getRequest()->getParam($idFieldName);
        $product = Mage::getModel('catalog/product');

        if ($productId) {
            $product->load($productId);
        }

        Mage::register('current_product', $product);
        return $this;
    } 
}