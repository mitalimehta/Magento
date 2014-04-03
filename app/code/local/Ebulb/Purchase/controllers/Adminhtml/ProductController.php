<?php

class Ebulb_Purchase_Adminhtml_ProductController extends Mage_Adminhtml_Controller_action
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
        $this->_title($this->__('Contacts'))->_title($this->__('Manage Products'));
      
        $this->loadLayout();
       
        $this->_setActiveMenu('purchase/product');
     
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Products'), Mage::helper('purchase')->__('Products'));
        $this->_addBreadcrumb(Mage::helper('purchase')->__('Manage Products'), Mage::helper('purchase')->__('Manage Products'));

        $this->renderLayout();
    }
    
    public function saveajaxAction() {   
        
        $ok = true;
        $msg = '';
        
        try 
        {
           if ($data = $this->getRequest()->getPost()) {
            
               $productId = Mage::getModel("catalog/product")->getIdBySku($this->getRequest()->getPost('sku'));
               if(!$productId){
                   $ok = false;
                   $msg = 'Invalid SKU!';
               } 
                   
               if($ok){
                    $model = Mage::getModel('purchase/product');        
                
                    $model->setData($data)
                        ->setData('vendor_id',(int)Mage::app()->getRequest()->getParam('parent_id'))
                        ->setData('product_id',$productId);
                    
                    if($this->getRequest()->getParam('product_id'))
                        $model->setData('vendor_product_id',$this->getRequest()->getParam('product_id'));
                     
                    //$model->save(); 
                    
                    if(!$model->checkexistingproduct())
                    {
                        $ok = false;
                        $msg = 'Record Already Exists';    
                    }
                    else{
                        //echo "else";exit;
                        $model->save(); 
                    }
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
                      ->createBlock('purchase/adminhtml_vendor_edit_tab_product', 'ProductGrid')
                      ->setParentId($parent_id); 
                      
         //echo $block->toHtml();             
        $this->getResponse()->setBody($block->toHtml());              
    }
    
    public function editajaxAction()
    {
        
        $contactid = (int)Mage::app()->getRequest()->getParam('vendor_product_id');
      
        $block = $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_productedit', 'productedit');
        $block->loadProduct($contactid);
        $block->setGuid(Mage::app()->getRequest()->getParam('guid'));                 
        $block->setTemplate('purchase/editproduct.phtml');
        $this->getResponse()->setBody($block->toHtml());                  
        
    }
    
    public function deleteajaxAction()
    {
        $ok = true;
        $msg = 'Contact deleted';
        
        try 
        {
            $vendor_product_id = (int)Mage::app()->getRequest()->getParam('vendor_product_id');
            $vendorproduct    = Mage::getModel('purchase/product')->load($vendor_product_id);
            if($vendorproduct)
               $vendorproduct->delete();
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
    
    public function validateAction()
    {
        
        $product = null; 
        
        if($id = Mage::getModel("catalog/product")->getIdBySku($this->getRequest()->getParam('sku'))){   
           $product = Mage::getModel("catalog/product")->load($id); 
        }
        if($product){
           $response= "<strong>".$product->getName()."</strong>";
        }
        else
           $response= "<font color=\"red\">*Invalid Sku</font>";   
        
        $this->getResponse()->setBody($response);    
        
    }
    
}
