<?php

class Ebulb_Productgroup_AttributeController extends Mage_Core_Controller_Front_Action
{ 
    protected $_attributeid;
    protected $_attributestr;
    protected $_attributecode;
    protected $_productEntityTypeId; 
    protected $_removefunction; 
    
    protected $_groupid; 
    
    public function getattributevaluesAction(){
        
        $this->_attributeid  = $this->getRequest()->getParam('attributeid'); 
        $this->_attributecode  = $this->getRequest()->getParam('attributecode'); 
        
        $this->_groupid  = $this->getRequest()->getParam('id'); 
        $this->_productEntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();  
       
        $_attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $this->_attributecode);
       
        
        $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                    ->setPositionOrder('asc')
                                    ->setAttributeFilter($this->_attributeid) 
                                    ->setStoreFilter()
                                    ->load();
    
        $options = array();
        foreach($attributeOptionSingle as $value) {
            $options[$value->getOptionId()] = $value->getValue();
        }
       
        $this->_removefunction  = "removeoptions('$this->_attributecode')";
           
        $html  = '';
    
        $html .= $this->_attributecode.'~~~~~######~~~~~######~~~~~';
    
        //$html .= '<div id="attributeoptions_'.$this->_attributecode.'">';
        
        $html .= '<table cellspacing="4" cellpadding="4" width="500">';
        $html .= '<tr>';
        $html .= '<td width="200">';
        $html .= $_attribute->getFrontendLabel()." : ";
        $html .= '</td>';
        
        $html .= '<td width="200">'; 
        $html .= '<select multiple size="6" name="attributeoptionsselect_'.$this->_attributeid.'[]" id="attributeoptionsselect_'.$this->_attributeid.'">';   
        
        foreach($options as $key=>$val)
        {
            $html .= "<option value='".$key."'>".$val."</option>";
        }
        $html .= '</select>';   
        $html .= '<input type="hidden" name="attributeshdn[]" value="'.$this->_attributeid.'">';   
        $html .= '<input type="hidden" name="attributescodehdn[]" value="'.$this->_attributecode.'">';   
        //$html .= '<input type="hidden" name="attrcodehdn" id="attrcodehdn" value="'.$this->_attributecode.'">';   
        $html .= '</td>';
        
        $html .= '<td width="100" align="left">'; 
        $html .= '<a href="#" onclick="'.$this->_removefunction.'">remove</a>';  
        $html .= '</td>';  
        
        $html .= '</tr>';   
        $html .= '</table>';   
        //$html .= '</div>';
        
        //$attributesdata = Mage::getModel('productgroup/productgroup')->getattributesbygroupid($this->getRequest()->getParam('id'),$this->_attributeid);
                   
        //echo "<pre>"; print_r($options); echo "</pre>";
        echo $html;
        
        return;
    }
    
    public function getproductidAction(){
        $productsku = base64_decode($this->getRequest()->getParam('productsku'));
        $groupid = $this->getRequest()->getParam('groupid');
        $optionstr = base64_decode($this->getRequest()->getParam('optionstr'));
        
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
          
            $group = Mage::getModel('productgroup/productgroup')->getgroupid($product_id);  
            if(count($group) > 0){ 
                if($group[0]['Groupid'] == $groupid && $group[0]['option_indent'] == $optionstr){
                    return;
                }
                else{
                    echo "This sku is already added";
                }
            }   
          } 
          return;
    }
    
    public function submitproductoptionAction(){ 
        
        $attributes = array();
        $attributes = $this->getRequest()->getParam('attributes');
        
        $optionstr = '';
        $groupid = $this->getRequest()->getParam('groupid');      
        foreach($attributes as $key=>$val){
            if($optionstr == '') $optionstr = $this->getRequest()->getParam('optionsselect_'.$val);
            else $optionstr .= "," . $this->getRequest()->getParam('optionsselect_'.$val); 
        }
        
        $product_id = Mage::getModel('productgroup/productgroup')->getproductidbycombination($groupid,$optionstr);
        
        $product_model = Mage::getModel('catalog/product');      
        try{$_product = $product_model->load($product_id);}catch(Exception $e){
                echo "*Invalid code:$product_id<br> ";
                flush();
                continue;}
           
            if($_product){   
                 $product_url = $_product->getProductUrl();   
                 $this->_redirectUrl($product_url);
            }
            else {
                 $product_url = '';
                 exit;
            }
    }

}