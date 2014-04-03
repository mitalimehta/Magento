<?php

class Ebulb_Automaticfeed_Adminhtml_CompanyController extends Mage_Adminhtml_Controller_action
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
        $this->_title($this->__('Company'))->_title($this->__('Manage Company'));
      
        $this->loadLayout();
       
        $this->_setActiveMenu('catalog/automaticfeed');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Contacts'), Mage::helper('purchase')->__('Company'));
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Manage Contacts'), Mage::helper('purchase')->__('Manage Company'));

        $this->renderLayout();
    }
    
    /**
     * vendor edit action
     */
     public function editAction() { 
         
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('automaticfeed/company')->load($id);
        
        if ($model->getId() || $id == 0) {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {   
                $model->setData($data);
            }

            Mage::register('current_company', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/automaticfeed');

            $this->_addBreadcrumb(Mage::helper('automaticfeed')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('automaticfeed')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('automaticfeed/adminhtml_company_edit'));
                //->_addLeft($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tabs'));
           
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('automaticfeed')->__('Item does not exist'));
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
            
            $model = Mage::getModel('automaticfeed/company');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id')); 
            
            try {
                
                $model->save(); 
        
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('automaticfeed')->__('Item was successfully saved'));
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
                $model = Mage::getModel('automaticfeed/company');
                 
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
        
        $contactIds = $this->getRequest()->getParam('automaticfeed');
        
        if(!is_array($contactIds)) { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else { 
            try {
                foreach ($contactIds as $contactId) {
                    $contact = Mage::getModel('automaticfeed/company')->load($contactId);
                    $contact->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($contactIds)
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
        $fileName   = 'company.csv';
        $content    = $this->getLayout()->createBlock('automaticfeed/adminhtml_company_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'company.xml';
        $content    = $this->getLayout()->createBlock('automaticfeed/adminhtml_company_grid')
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