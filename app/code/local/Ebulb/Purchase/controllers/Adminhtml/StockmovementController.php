<?php

class Ebulb_Purchase_Adminhtml_StockmovementController extends Mage_Adminhtml_Controller_action
{
    public function indexAction()
    {  
        $this->_title($this->__('Purchase'))->_title($this->__('All Stock Movement'));
      
        $this->loadLayout();
       
        $this->_setActiveMenu('purchase/tools');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Purchase'), Mage::helper('purchase')->__('All Stock Movement'));
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Manage Purchase'), Mage::helper('purchase')->__('All Stock Movement'));
        
        $this->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_stockmovement_grid')->toHtml());
    }
     
    public function exportCsvAction()
    {
        $fileName   = 'allstockmovement.csv';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_stockmovement_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'allstockmovement.xml';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_stockmovement_grid')
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

?>