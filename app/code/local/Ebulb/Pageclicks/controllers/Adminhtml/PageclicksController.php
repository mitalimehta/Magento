<?php

class Ebulb_Pageclicks_Adminhtml_PageclicksController extends Mage_Adminhtml_Controller_action
{ 
    protected $_productattributeshdn = array();  
    protected $_productattributescodehdn = array();  
    protected $_productattributeoptionshdn = array();  
    
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('pageclicks/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        
        return $this;
    }   
 
    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('pageclicks/pageclicks')->load($id);
       
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('pageclicks_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('pageclicks/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('pageclicks/adminhtml_pageclicks_edit'))
                ->_addLeft($this->getLayout()->createBlock('pageclicks/adminhtml_pageclicks_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pageclicks')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    
    public function productskuAction() {
        
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('pageclicks/pageclicks')->load($id);
       
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('pageclicks_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('pageclicks/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('pageclicks/adminhtml_pageclicks_productsku'))
                ->_addLeft($this->getLayout()->createBlock('pageclicks/adminhtml_pageclicks_productsku_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pageclicks')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction() {
        $this->_forward('edit');
    }
 
    public function saveAction() {
        
        if ($data = $this->getRequest()->getPost()) {
            
        /*    if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {    
                    // Starting upload     
                    $uploader = new Varien_File_Uploader('filename');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                            
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS ;
                    $uploader->save($path, $_FILES['filename']['name'] );
                    
                } catch (Exception $e) {
              
                }
            
                //this way the name is saved in DB
                  $data['filename'] = $_FILES['filename']['name'];
            }*/
              
           
            $model = Mage::getModel('pageclicks/pageclicks');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'))
                ->setWebsiteId(1);
            
            /*$this->_productattributeshdn      = $this->getRequest()->getParam('attributeshdn'); 
            $this->_productattributescodehdn  = $this->getRequest()->getParam('attributescodehdn'); 
            
            $productattributes = array();*/
            try {
                 
                $model->save(); 
                
                /*if(is_array($this->_productattributeshdn))
                    $productattributes = $this->_productattributeshdn;
                        
                $model->savegroupattributes($productattributes,$this->_productattributescodehdn,$model->getId());
                
                foreach($productattributes as $val){
                    $this->_productattributeoptionshdn = $this->getRequest()->getParam('attributeoptionsselect_'.$val);
                    $model->savegroupattributeoptions($this->_productattributeoptionshdn,$val);     
                }
                 
                $model->saveproducts(); */
               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pageclicks')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pageclicks')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
 
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('pageclicks/pageclicks');
                 
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
       
        $groupIds = $this->getRequest()->getParam('pageclicks');
        if(!is_array($groupIds)) { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else { 
            try {
                foreach ($groupIds as $groupId) {
                    $group = Mage::getModel('pageclicks/pageclicks')->load($groupId);
                    $group->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($groupIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
//    public function massStatusAction()
//    {
//        $testimonialIds = $this->getRequest()->getParam('testimonial');
//        if(!is_array($testimonialIds)) {
//            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
//        } else {
//            try {
//                foreach ($testimonialIds as $testimonialId) {
//                    $testimonial = Mage::getSingleton('testimonial/testimonial')
//                        ->load($testimonialId)
//                        ->setApproved($this->getRequest()->getParam('status'))
//                        ->setIsMassupdate(true)
//                        ->save();
//                        //echo $this->getRequest()->getParam('status');
//                        //exit();
//                }
//                $this->_getSession()->addSuccess(
//                    $this->__('Total of %d record(s) were successfully updated', count($testimonialIds))
//                );
//            } catch (Exception $e) {
//                $this->_getSession()->addError($e->getMessage());
//            }
 //       }
//        $this->_redirect('*/*/index');
 //   }
  
    public function exportCsvAction()
    {
        $fileName   = 'pageclicks.csv';
        $content    = $this->getLayout()->createBlock('pageclicks/adminhtml_pageclicks_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'pageclicks.xml';
        $content    = $this->getLayout()->createBlock('pageclicks/adminhtml_pageclicks_grid')
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