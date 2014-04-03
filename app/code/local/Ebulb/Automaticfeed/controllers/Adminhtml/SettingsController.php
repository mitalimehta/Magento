<?php

class Ebulb_Automaticfeed_Adminhtml_SettingsController extends Mage_Adminhtml_Controller_action
{
    protected $_productEntityTypeId;  
    
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
        $this->_title($this->__('Settings'))->_title($this->__('Manage Settings'));
      
        $this->loadLayout();
       
        $this->_setActiveMenu('catalog/automaticfeed');
     
        $this->_addBreadcrumb(Mage::helper('automaticfeed')->__('Settings'), Mage::helper('automaticfeed')->__('Settings'));
        $this->_addBreadcrumb(Mage::helper('automaticfeed')->__('Manage Settings'), Mage::helper('automaticfeed')->__('Manage Settings'));

        $this->renderLayout();
    }
    
    /**
     * vendor edit action
     */
     public function editAction() { 
         
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('automaticfeed/autofeedinfo')->load($id);
        
        if ($model->getId() || $id == 0) {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {   
                $model->setData($data);
            }

            Mage::register('current_settings', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/automaticfeed');

            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('purchase')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('automaticfeed/adminhtml_settings_edit'));
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
            
            $model = Mage::getModel('automaticfeed/autofeedinfo');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id')); 
            
            try {
               
                $model->save(); 
                
                $company_attr  = Mage::app()->getRequest()->getParam('company_attr');
                $attribute_id  = Mage::app()->getRequest()->getParam('attribute_id');
                $custom_value  = Mage::app()->getRequest()->getParam('custom_value');
                $sort_order    = Mage::app()->getRequest()->getParam('sort_order');
                
                $model->saveAttributes($company_attr,$attribute_id,$custom_value,$sort_order,$model->getId());
        
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('automaticfeed')->__('Unable to find item to save'));
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
                    $contact = Mage::getModel('automaticfeed/autofeedinfo')->load($contactId);
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
        $fileName   = 'autofeed.csv';
        $content    = $this->getLayout()->createBlock('automaticfeed/adminhtml_settings_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'autofeed.xml';
        $content    = $this->getLayout()->createBlock('automaticfeed/adminhtml_settings_grid')
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
    
    public function getattributeAction(){
        $counter = Mage::app()->getRequest()->getParam('counter');
        
        $company_attr  = base64_decode(Mage::app()->getRequest()->getParam('company_attr'));
        $attribute_id  = Mage::app()->getRequest()->getParam('attribute_id');       
        $sort_order    = Mage::app()->getRequest()->getParam('sort_order');       
        $custom_value  = Mage::app()->getRequest()->getParam('custom_value');       
        
        $html  = '';
        
        $html .= '<table cellspacing="4" cellpadding="4" width="100%">';
        
        $html .= '<tr>';
        $html .= '<td class="value">'; 
        $html .= '<input type="text" class="input-text" name="company_attr[]" id="company_attr_'.$counter.'" value="'.$company_attr.'"  />';
        $html .= '</td>';
        
        $this->_productEntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();

          $attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')  
                                ->setEntityTypeFilter($this->_productEntityTypeId)
                                //->addFieldToFilter('frontend_input', array('select','multiselect')) 
                                ->getData();                     
        
        $html .= '<td class="value">'; 
        $html .= '<select class="select" name="attribute_id[]" id="attribute_id_'.$counter.'" onchange="showcustomvalue(this.value,'.$counter.');">';   
            
            $html .= "<option value=''>Select Attribute</option>";
            foreach($attributesInfo as $attribute){ 
                
                if($attribute_id == $attribute['attribute_id'])
                    $selected = " selected = 'selected' ";
                else
                    $selected = '';
                    
                
                $html .= "<option value='".$attribute['attribute_id']."' ".$selected.">".$attribute['frontend_label']."</option>";      
            }
            
            if($attribute_id == 0 && $custom_value != '')  
                $selected = " selected = 'selected' ";
            else
                $selected = '';
            
            $html .= "<option value='0' ".$selected."  >Custom Value</option>";  
        
        $html .= '</select>';   
        
        if($attribute_id == 0 && $custom_value != '')
            $display = ""; 
        else
            $display = "style='display:none;' ";
                 
        $html .= '<div id="custom_value_div_'.$counter.'" '.$display.' ><br /><input type="text" class="input-text" name="custom_value[]" id="custom_value_'.$counter.'" value="'.$custom_value.'" /></div>';
        $html .= '</td>';
        
        $html .= '<td class="value">'; 
        $html .= '<input type="text" name="sort_order[]" id="sort_order_'.$counter.'" style="width:20px;" value="'.$sort_order.'" />';
        $html .= '</td>'; 
        
        $html .= '<td class="value">'; 
        
        $html .= '<button type="button" class="scalable add" onclick="deleteattribute('.$counter.');";>
                    <span>Remove</span>
                  </button>';
        $html .= '</td>';  
        
        $html .= '</tr>';   
        $html .= '</table>';  
        
        echo $html; 
    }
}

?>