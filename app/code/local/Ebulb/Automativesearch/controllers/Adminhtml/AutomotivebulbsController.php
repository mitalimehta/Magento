<?php

class Ebulb_Automativesearch_Adminhtml_AutomotivebulbsController extends Mage_Adminhtml_Controller_action
{
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
        //echo $id."-------";
        $model  = Mage::getModel('automativesearch/automativesearch')->load($id);
        //echo $model->getId();exit;
        if ($model->getId() || $id == 0) {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {   
                $model->setData($data);
            }

            Mage::register('current_model', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/customsearch');

            $this->_addBreadcrumb(Mage::helper('customsearch')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('customsearch')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('automativesearch/adminhtml_automotivebulbs_edit')); 

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
    
    public function saveAction() {
        
        if ($data = $this->getRequest()->getPost()) {
           
            $model = Mage::getModel('automativesearch/automativesearch');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id')); 
            
            try {
                $success                     = false;
                    
                $counter                     = $data['counter'];
                $car_manufacturer_id         = $data['car_manufacturer_id_hdn']?$data['car_manufacturer_id_hdn']:$data['car_manufacturer_id'];
                $car_manufacturer_year_id    = $data['car_manufacturer_year_id_hdn']?$data['car_manufacturer_year_id_hdn']:$data['car_manufacturer_year_id'];
                $car_manufacturer_model_id   = $data['car_manufacturer_model_id_hdn']?$data['car_manufacturer_model_id_hdn']:$data['car_manufacturer_model_id'];
                $car_manufacturer_type_id    = $data['car_manufacturer_type_id_hdn']?$data['car_manufacturer_type_id_hdn']:$data['car_manufacturer_type_id'];
                
                $automotivebulbs = array();
                
                foreach($counter as $count)
                {
                    $automotivebulbs['entity_id']                           = $model->getId();
                    $automotivebulbs[$count]['location_id']                 = $data['car_bulb_location_'.$count];     
                    $automotivebulbs[$count]['product_id']                  = $data['product_sku_'.$count];     
                    $automotivebulbs['car_manufacturer_id']                 = $car_manufacturer_id;   
                    $automotivebulbs['car_manufacturer_year_id']            = $car_manufacturer_year_id;   
                    $automotivebulbs['car_manufacturer_model_id']           = $car_manufacturer_model_id;   
                    $automotivebulbs['car_manufacturer_type_id']            = $car_manufacturer_type_id;   
                }
                
                $success = $model->saveautomotivebulbs($automotivebulbs); 
                
                if($success > 0 && is_numeric($success)){
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('automaticfeed')->__('Item was successfully saved'));
                }
                else{
                   Mage::getSingleton('adminhtml/session')->addError($success);  
                }
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) 
                {  
                    if($success > 0 && is_numeric($success))
                        $this->_redirect('*/*/edit', array('id' => $success));
                    else
                        $this->_redirect('*/*/edit');  
                        
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
 
    public function addmoreAction(){
        $counter = $this->getRequest()->getParam('count');
        $car_location         = Mage::getModel('automativesearch/automativesearch')->getAllOptions('car_bulb_location');     
        $html="";
        
        $locationid  = $this->getRequest()->getParam('locationid');
        
        $html.="<td colspan='4'></td>";
        $html.="<td align='right'><button type='button' class='scalable delete' onclick='removelocation(".$counter.");';>Remove</button></td>";
        $html.="<td align='right'>
                    <input name='counter[]' value='".$counter."' type='hidden'>
                    <select name='car_bulb_location_".$counter."' id='car_bulb_location_".$counter."' style='width: 200px;'> 
                        <option value=''>-- Select --</option> ";
                        foreach($car_location as $key=>$val){
                            if($locationid == $key){
                                $selected = " selected = 'selected' ";
                            }
                            else{
                               $selected = ""; 
                            }
        $html.="            <option value='".$key."' ".$selected.">".$val."</option>";
                        } 
        $html.="    </select> 
                </td>";
                
        $html.="<td align='left' id='products_sku_".$counter."' colspan='2'><input class='input-text' onblur='Checkproductsku(this.value)' name='product_sku_".$counter."[]'></td>";        
        //$html.="<td align='left'></td>";        
        $html.="<td align='right'><button type='button' class='scalable add' onclick='addproducts(".$counter.");';><span>ADD PRODUCT</span></button></td>";        
        //$html.="<td align='left'><button type='button' class='scalable add' onclick='createfeeds();';><span>Remove</span></button></td>";        
        
        echo $html;return;
    }
    
    public function addproductAction(){
        $counter = $this->getRequest()->getParam('count');
        $productcounter = $this->getRequest()->getParam('productcounter');
        $sku = $this->getRequest()->getParam('sku');
        $html="";
        $html.="<br />"; 
        $html.="<input class='input-text' name='product_sku_".$counter."[]' value='".$sku."' onblur='Checkproductsku(this.value)'>";
        $html.="&nbsp;<button type='button' class='scalable delete' onclick='removeproduct(".$productcounter.");';>Remove</button>";         
        echo $html;return;
    }
    
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('automativesearch/automativesearch');
                 
                $model->load($this->getRequest()->getParam('id'))
                    ->deletecombination();
                     
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
       
        $Ids = $this->getRequest()->getParam('automotivesearch');
        
        if(!is_array($Ids)) { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else { 
            try {
                foreach ($Ids as $Id) {
                    $automativesearch = Mage::getModel('automativesearch/automativesearch')->load($Id);
                    $automativesearch->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($Ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    
    public function checkproductskuAction(){
        $productsku = base64_decode($this->getRequest()->getParam('productsku'));
        
        $product_model = Mage::getModel('catalog/product'); 
        try{$product = $product_model->loadByAttribute('sku',$productsku);}catch(Exception $e){
            echo "*Invalid code:$productsku<br> ";
            flush();
            continue;}   
          
          if($product) {
            $product_id = $product->getId();    
          }
          else {
            $product_id = '';
          } 
          
          if($product_id == ''){
            echo "invalid sku";return;
          }
          else{
              
              if($product->getStatus() == 2){
                echo "This product is disabled";    
                return;
              }
              
              if($product->getVisibility() != 2 && $product->getVisibility() != 3 && $product->getVisibility() != 4)
              {
                 echo "This product is not visible";    
                 return;
              }
            
          } 
          return;
    }
}