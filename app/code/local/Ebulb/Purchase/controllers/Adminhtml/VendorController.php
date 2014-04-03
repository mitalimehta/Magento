<?php

class Ebulb_Purchase_Adminhtml_VendorController extends Mage_Adminhtml_Controller_action
{
    protected function _initVendor($idFieldName = 'id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Vendors'));

        $vendorId = (int) $this->getRequest()->getParam($idFieldName);
        $vendor = Mage::getModel('purchase/vendor');

        if ($vendorId) {
            $vendor->load($vendorId);
        }

        Mage::register('current_vendor', $vendor);
        return $this;
    } 
    
    public function indexAction()
    { 
        $this->_title($this->__('Vendors'))->_title($this->__('Manage Vendors'));
      
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();
       
        $this->_setActiveMenu('purchase/vendor');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Vendors'), Mage::helper('purchase')->__('Vendors'));
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Manage Vendors'), Mage::helper('purchase')->__('Manage Vendors'));

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
        $model  = Mage::getModel('purchase/vendor')->load($id);
        
        if ($model->getId() || $id == 0) {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {   
                $model->setData($data);
            }

            Mage::register('current_vendor', $model);

            $this->loadLayout();
            $this->_setActiveMenu('purchase/vendor');

            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit'))
                ->_addLeft($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('purchase')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Create new vendor action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    
    public function saveAction() {
        
        if ($data = $this->getRequest()->getPost()) {
            
            $model = Mage::getModel('purchase/vendor');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id')); 
            
            try {
                
                $model->save(); 
        
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productgroup')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productgroup')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
    
    
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('purchase/vendor');
                 
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        
        $vendorIds = $this->getRequest()->getParam('purchase');
        
        if(!is_array($vendorIds)) { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else { 
            try {
                foreach ($vendorIds as $vendorId) {
                    $vendor = Mage::getModel('purchase/vendor')->load($vendorId);
                    $vendor->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'vendors.csv';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_vendor_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendors.xml';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_vendor_grid')
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
        $this->_initVendor();
        //$this->loadLayout();    
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml());   
        //$this->renderLayout();  
        //die;
        //echo $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml();exit;
    }
    
    public function lastOrdersAction() {
        $this->_initVendor();
        $this->loadLayout();    
        $this->getResponse()->setBody($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml());
        $this->renderLayout();  
    }
    
}

?>