<?php

class Ebulb_Purchase_Adminhtml_InventorycostController extends Mage_Adminhtml_Controller_action
{
    public function indexAction()
    {
        
        $this->_title($this->__('Inventory Cost'));
      
        
        $this->loadLayout();
       
        $this->_setActiveMenu('purchase/inventorycost');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Inventory Cost'), Mage::helper('purchase')->__('Inventory Cost'));

        
        $this->renderLayout();
    }
    
    
    public function exportCsvAction()
    {
        $fileName   = 'inventorycost_summary.csv';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_inventorycost_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'inventorycost_summary.xml';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_inventorycost_grid')
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