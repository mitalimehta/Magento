<?php

class Ebulb_Customsearch_Adminhtml_SearchattributeController extends Mage_Adminhtml_Controller_action
{
    protected $_attributeid;  
    protected $_attributesetid;  
    
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('catalog/customsearch')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
       
        return $this;
    }   
 
    public function indexAction() {  
        $this->_initAction();
        $this->renderLayout();  
    } 
 
     public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('customsearch/searchattributeentity')->load($id);
       
        if ($model->getId() || $id == 0) {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {   
                $model->setData($data);
            }

            Mage::register('current_search', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/customsearch');

            $this->_addBreadcrumb(Mage::helper('customsearch')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('customsearch')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('customsearch/adminhtml_searchattribute_edit')); 

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customsearch')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
   
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    
    public function exportCsvAction()
    {
        $fileName   = 'search.csv';
        $content    = $this->getLayout()->createBlock('customsearch/adminhtml_searchattribute_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'search.xml';
        $content    = $this->getLayout()->createBlock('customsearch/adminhtml_searchattribute_grid')
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
    
    public function getattributesetsAction(){
        
        $_Selectedattset = $this->getRequest()->getParam('selected');
        
        $this->_setTypeId();  
        
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter(Mage::registry('entityType'));  
        
        $html = '<select name="type_entity_id" id="type_entity_id" onchange="javascript:getAttributes(this.value);" class="required-entry">';
        $html .= '<option value="">--please select--</option>';
        foreach($collection as $attributeset){
            if($_Selectedattset == $attributeset['attribute_set_id'])
                $selected = " selected='selected' ";
            else
                $selected = "";
            $html.= "<option value='".$attributeset['attribute_set_id']."' ".$selected." >".$attributeset['attribute_set_name']."</option>";     
        }
        $html .='</select>';    
        
        echo $html;
        return;
    }
    
    protected function _setTypeId()
    {
        Mage::register('entityType',
            Mage::getModel('catalog/product')->getResource()->getTypeId());
    }
    
    public function getattributesAction(){
        
        $this->_attributesetid = $this->getRequest()->getParam('entity_id');
        
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                        //->setAttributeSetsFilter($this->_attributesetid)  
                        ->addFieldToFilter('frontend_input', array('select','multiselect')) 
                        ->load(); 
        
        if($this->_attributesetid)
            $collection->setAttributeSetsFilter($this->_attributesetid);   
        
        $html = '<select name="type_attribute_id" id="type_attribute_id"">';
        foreach($collection as $attribute){
            $html.= "<option value='".$attribute['attribute_id']."'>".$attribute['frontend_label']."</option>";         
        }
        $html .='</select>&nbsp;&nbsp;<button class="scalable" onclick="javascript:addattribute(document.getElementById(\'type_attribute_id\').value,0);" type="button"><span>Add<span></button>';       
        $html .= "<table class='form-list' width='100%' cellspacing='2'><tr><td width='25%' class='label'></td><td  width='20%'><br /><b>sort order</b></td><td width='25%'></td></tr></table>";    
        
        echo $html; 
        return;
    }
    
    public function getallattributesAction(){
        
        $_Selectedattset = $this->getRequest()->getParam('selected');      
        
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')  
                        ->addFieldToFilter('frontend_input', array('select','multiselect')) 
                        ->load();
                        
        $html = '<select name="type_entity_id" id="type_entity_id" class="required-entry" onchange="javascript:loadattributevalues(this.value);">';
        $html .= '<option value="">--please select--</option>';
        foreach($collection as $attribute){
            if($_Selectedattset == $attribute['attribute_id'])
                $selected = " selected='selected' ";
            else
                $selected = "";
            $html.= "<option value='".$attribute['attribute_id']."' ".$selected." >".$attribute['frontend_label']."</option>";         
        }
        $html .='</select>';       
        
        echo $html; 
        return;
        
    }
    
    public function loadattributevaluesAction(){
        
        $attribute_id =  $this->getRequest()->getParam('attribute_id');
       
        $_Selectedattset = $this->getRequest()->getParam('selected');  
       
        $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder('asc')
                                        ->setAttributeFilter($attribute_id) 
                                        ->setStoreFilter()
                                        ->load(); 
        
        $html = '<select name="type_entity_value" id="type_entity_value" class="required-entry">';
        $html .= '<option value="">--please select--</option>';
        foreach($attributeOptionSingle as $value){
            if($_Selectedattset == $value->getOptionId())
                $selected = " selected='selected' ";
            else
                $selected = "";
            $html.= "<option value='".$value->getOptionId()."' ".$selected." >".$value->getValue()."</option>";         
        }
        
        $html .='</select>';
        
        echo $html; 
        return;
    }
    
    public function addattributesAction(){
        $attribute_id     = $this->getRequest()->getParam('entity_id');
        
        $_attribute = Mage::getModel('eav/entity_attribute')->load($attribute_id);
        $attribute_text   = $_attribute->getFrontendLabel();
        $start            = $this->getRequest()->getParam('start');
        $sort_order       = $this->getRequest()->getParam('sort_order');      
        
        $html             = '';  
        if($start == 1) {   
            //$html .= "<table class='form-list' width='100%' cellspacing='2'><tr><td width='25%' class='label'></td><td  width='20%'>sort order</td><td width='25%'></td></tr></table>";    
        }
        
        $html .= "<table id='attribute_table_".$attribute_id."' class='form-list' width='100%' cellspacing='2'>
                    <tr>
                        <td width='25%' class='label' align='left'>".$attribute_text."</td>
                        <td width='15%' class='value' align='left'>
                            <input style='width:15px;' value=".$sort_order." name='attribute_sort_order_".$attribute_id."'>
                            <input value=".$attribute_id." type='hidden' name='attribute_id[]'>
                        </td>
                        <td width='25%'><button class='scalable' align='left' onclick='javascript:deleteattribute(".$attribute_id.");' type='button'><span>Remove<span></button></td>
                 </tr>
                 </table>
                 ";  
                 
        echo $html; 
        return;   
    }
    
    public function saveAction(){
        
        if ($data = $this->getRequest()->getPost()) {      
            
            $model = Mage::getModel('customsearch/searchattributeentity');        
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
          
           try {
                //echo "<pre>";print_r($model->getData());exit;
                if($model->checkExistingRecord($this->getRequest()->getParam('id'),$model->getData('type_id'),$model->getData('type_entity_id'))){
                    Mage::getSingleton('adminhtml/session')->addError("Record Already Exists");  
                    $this->_redirect('*/*/new');
                    return;      
                }
                else{
                    $model->save();
                    
                    $attributes             = $this->getRequest()->getParam('attribute_id');
                    
                    $attributes_sort_order = array();
                    
                    foreach($attributes as $attid){
                        $attributes_sort_order[$attid]     =  $this->getRequest()->getParam('attribute_sort_order_'.$attid);;
                    }
              
                    $model->saveSearchAttributes($attributes,$attributes_sort_order,$model->getId());
                    
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customsearch')->__('Item was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                }
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
       
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customsearch')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
    
    
    public function massDeleteAction() {
       
        $searchIds = $this->getRequest()->getParam('customsearch');
        if(!is_array($searchIds)) { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else { 
            try {
                foreach ($searchIds as $searchId) {
                    $search = Mage::getModel('customsearch/searchattributeentity')->load($searchId);
                    $search->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($searchIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function deleteAction() {
        
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('customsearch/searchattributeentity')->load($this->getRequest()->getParam('id'));
                $model->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    
    
    public function massStatusAction()
    {
        $searchIds = $this->getRequest()->getParam('customsearch');
        if(!is_array($searchIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($searchIds as $searchId) {
                    $search = Mage::getSingleton('customsearch/searchattributeentity')
                        ->load($searchId)
                        ->setEnabled($this->getRequest()->getParam('status'))
                        //->setIsMassupdate(true)
                        ->save();
                        //echo $this->getRequest()->getParam('status');
                        //exit();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($searchIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
}



?>