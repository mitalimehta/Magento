<?php

class Ebulb_Purchase_Adminhtml_VendorsproductController extends Mage_Adminhtml_Controller_action
{
    public function indexAction()
    { 
        $this->_title($this->__('Vendor\'s Products'));
      
        
        $this->loadLayout();
       
        $this->_setActiveMenu('purchase/vendorsproduct');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Vendor\'s Products'), Mage::helper('purchase')->__('Vendor\'s Products'));

        $this->renderLayout();
    }
    
    
    public function exportCsvAction()
    {
        $fileName   = 'vendorsproduct_summary.csv';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_vendorsproduct_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendorsproduct_summary.xml';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_vendorsproduct_grid')
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
    
}