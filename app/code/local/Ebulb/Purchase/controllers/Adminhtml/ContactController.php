<?php

class Ebulb_Purchase_Adminhtml_ContactController extends Mage_Adminhtml_Controller_action
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
        $this->_title($this->__('Contacts'))->_title($this->__('Manage Contacts'));
      
        $this->loadLayout();
       
        $this->_setActiveMenu('purchase/contact');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Contacts'), Mage::helper('purchase')->__('Contacts'));
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Manage Contacts'), Mage::helper('purchase')->__('Manage Contacts'));

        $this->renderLayout();
    }
    
    /**
     * vendor edit action
     */
     public function editAction() { 
         
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('purchase/contact')->load($id);
        
        if ($model->getId() || $id == 0) {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {   
                $model->setData($data);
            }

            Mage::register('current_contact', $model);

            $this->loadLayout();
            $this->_setActiveMenu('purchase/contact');

            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('purchase/adminhtml_contact_edit'));
                //->_addLeft($this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tabs'));
           
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
            
            $model = Mage::getModel('purchase/contact');        
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
    
    public function saveajaxAction() {
        
        
        $ok = true;
        $msg = '';
        
        try 
        {
           if ($data = $this->getRequest()->getPost()) {
            
               if($ok){
                    $model = Mage::getModel('purchase/contact');        
                
                    $model->setData($data)
                        ->setData('vendor_id',(int)Mage::app()->getRequest()->getParam('parent_id'));
                
                    if($this->getRequest()->getParam('contact_id'))
                        $model->setData('vendor_contact_id',$this->getRequest()->getParam('contact_id'));
                 
                    //echo "<pre>";print_r($model->getData());exit;
                    $model->save(); 
               }
           }        
        }
        catch (Exception $ex)
        {
            $msg = $ex->getMessage();
            $ok = false;
        }
        $response = array(
            'error'     => (!$ok),
            'message'   => $this->__($msg)
        );            
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);    
        
    }
    
    public function refreshListAction()
    {
        //recupere les infos
        $parent_id   = (int)Mage::app()->getRequest()->getParam('parent_id');
        
        $block = $this->getLayout()
                      ->createBlock('purchase/adminhtml_vendor_edit_tab_contact', 'ContactGrid')
                      ->setParentId($parent_id); 
                      
         //echo $block->toHtml();             
        $this->getResponse()->setBody($block->toHtml());              
    }
    
    public function editajaxAction()
    {
        
        $contactid = (int)Mage::app()->getRequest()->getParam('contact_id');
      
        $block = $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_edit', 'contactedit');
        $block->loadContact($contactid);
        $block->setGuid(Mage::app()->getRequest()->getParam('guid'));                 
        $block->setTemplate('purchase/editcontact.phtml');
        $this->getResponse()->setBody($block->toHtml());                  
        
    }
    
    public function deleteajaxAction()
    {
        $ok = true;
        $msg = 'Contact deleted';
        
        try 
        {
            $contact_id = (int)Mage::app()->getRequest()->getParam('contact_id');
            $contact    = Mage::getModel('purchase/contact')->load($contact_id);
            if($contact)
               $contact->delete();
        }
        catch (Exception $ex)
        {
            $msg = $ex->getMessage();
            $ok = false;
        }
        
        //Retourne
        $response = array(
            'error'     => (!$ok),
            'message'   => $this->__($msg)
        );            
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);    

    }
    
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('purchase/contact');
                 
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
        
        $contactIds = $this->getRequest()->getParam('purchase');
        
        if(!is_array($contactIds)) { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else { 
            try {
                foreach ($contactIds as $contactId) {
                    $contact = Mage::getModel('purchase/contact')->load($contactId);
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
        $fileName   = 'contacts.csv';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_contact_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'contacts.xml';
        $content    = $this->getLayout()->createBlock('purchase/adminhtml_contact_grid')
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