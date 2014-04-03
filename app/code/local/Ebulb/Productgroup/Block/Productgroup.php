<?php

class Ebulb_Productgroup_Block_productgroup extends Mage_Core_Block_Template{ 
     
    protected $_productid;
    protected $_groupid;
    protected $_grouparray = array();
    
    protected $_attributes = array();  
    protected $_attributevalues = array();  
    
    protected $_options = array(); 
    protected $_optionsvalues = array(); 
    protected $_optionsindentstr; 
    protected $_optionsindent = array(); 
    protected $_optionsproducts = array(); 
     
    public function __construct()
    {
   
    }
    
    public function getproductoptions(){ 
        
       $this->_grouparray = Mage::getModel('productgroup/productgroup')->getgroupid($this->_productid);   
       
       if(count($this->_grouparray) > 0){
           
            $this->_groupid = $this->_grouparray[0]['Groupid'];             
            $this->_attributes = Mage::getModel('productgroup/productgroup')->getattributesbygroupid($this->_groupid);
            
            $this->_optionsindentstr = $this->_grouparray[0]['option_indent']; 
            
            foreach($this->_attributes as $key=>$val)
            {
                $this->_options[$val['attribute_code']] = Mage::getModel('productgroup/productgroup')->getattributeoptionsbygroupid($this->_groupid,$val['attribute_id']);   
                
                $this->_attributevalues[$val['attribute_code']] = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $val['attribute_code'])->getFrontendLabel();
                
                $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                            ->setPositionOrder('asc')
                                            ->setAttributeFilter($val['attribute_id']) 
                                            ->setStoreFilter()
                                            ->load();  
                
                foreach($attributeOptionSingle as $value){
                    $this->_optionsvalues[$val['attribute_code']][$value->getOptionId()] = $value->getValue();
                }
                
                $this->_optionsindent = explode(",",$this->_optionsindentstr);
                
            }
            
            $attributeoptionsarr = array();
            $attributeoptionscombinationarr = array();
            $counter = 0;
             
            foreach($this->_options as $key=>$val){  
                
                $counter1 = count($this->_options[$key]);
                
                for($i=0;$i<$counter1;$i++){
                    $attributeoptionsarr[$counter][$i] = $val[$i]['option_id'];
                }
                $counter++;    
            }
            
            $attributeoptionscombinationarr = $this->helper('productgroup/data')->combineItems($attributeoptionsarr); 
                
            $counter = 0;
            foreach($attributeoptionscombinationarr as $key=>$val){
                if(is_array($val))
                    $tmparr = array_reverse($val);
                else
                    $tmparr = $val;     
                    
                $attributeoptionscombinationarr[$counter] = $tmparr; 
                $counter++;
            }
            
            $counter = 0;  
            foreach($attributeoptionscombinationarr as $key=>$val){ 
                $optionsidstr = '';
                    if(is_array($val))  
                        $optionsidstr = implode(",",$val); 
                    else
                        $optionsidstr = $val;
                
              $this->_optionsproducts[$counter]['combination'] = $optionsidstr;    
              $this->_optionsproducts[$counter]['product_id']  = Mage::getModel('productgroup/productgroup')->getproductidstorefilter($this->_groupid,$optionsidstr); 
              $counter++;     
            }
            
            foreach($this->_optionsproducts as $value){
                if($this->_optionsindentstr != $value['combination']){
                    foreach($this->_optionsindent as $optionindent){ 
                        if(($value['product_id'] == 0 || $value['product_id'] == '') ){   
                            $currentarr = explode(",",$this->_optionsindentstr);
                            $noprodarr  = explode(",",$value['combination']); 
                            $optionrem  = array_diff($noprodarr,$currentarr); 
                            
                            if(count($optionrem) == 1){
                                foreach($this->_attributes as $key=>$val)
                                {  
                                    foreach($this->_options[$val['attribute_code']] as $count=>$optionid){
                                        if(in_array($optionid['option_id'],$optionrem)){
                                            unset($this->_options[$val['attribute_code']][$count]);     
                                        } 
                                    } 
                                }
                            } 
                        }        
                          
                    }
                }   
            } 
       } 
    }
    
    public function _getproductoptions($productid){
        $this->_productid = $productid;  
        $this->getproductoptions();     
    }
    
    public function getFormActionUrl(){
        return Mage::getUrl('productgroup/attribute/submitproductoption');
    }
}