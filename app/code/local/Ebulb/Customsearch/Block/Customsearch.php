<?php 

class Ebulb_Customsearch_Block_Customsearch extends Mage_Catalog_Block_Product_List{   
    
    public $_categorySearchID        = null;
    public $_typeID                  = null;
    public $_categoryID              = null;
    public $_typeEntityID            = null;
    public $_typeEntityValue         = null;
   
    public $_searchAttributes        = array();
    
    public $_attribute               = array();
    public $_attributeId             = array();
    public $_currentSearchCriteria   = array();
    
    public $_productCollection       = null;
    
    public function _construct()
    {   
        if(Mage::registry('current_search_criteria'))
            $this->_currentSearchCriteria  = Mage::registry('current_search_criteria');
      
        $this->_categorySearchID           = $this->_currentSearchCriteria['entity_id'];
        
        $this->_typeID                     = $this->_currentSearchCriteria['type_id'];
        $this->_categoryID                 = $this->_currentSearchCriteria['category_id'];
        $this->_typeEntityID               = $this->_currentSearchCriteria['type_entity_id'];
        $this->_typeEntityValue            = $this->_currentSearchCriteria['type_entity_value'];
                
        if($this->_categorySearchID){
            $this->_searchAttributes       = Mage::getModel('customsearch/searchattributeentity')->getSearchAttributes($this->_categorySearchID);
        }
        
        foreach($this->_searchAttributes as $searchattribute){ 
            $this->_attribute[$searchattribute['attribute_id']] = Mage::getModel('eav/entity_attribute')->load($searchattribute['attribute_id']);
            $this->_attributeId[$searchattribute['sort_order']] = $searchattribute['attribute_id'];    
        } 
      
    }
    
    public function getAction()
    {
        return Mage::getUrl('customsearch/customsearchresult');
    }
    
    public function getProjectorSeachAttributes(){   
        
        return $this->_searchAttributes;
    }
    
    public function getattributeoptions($attribute = array()){
        $counter = 0;
        $options = array();
       
        if(array_key_exists('attribute_id',$attribute)){
            
              /*$this->_attribute[$attribute['attribute_id']] = Mage::getModel('eav/entity_attribute')->load($attribute['attribute_id']);
              $this->_attributeId[$attribute['sort_order']] = $attribute['attribute_id'];  */
              
              if($attribute['sort_order'] == 0)
              {                             
                $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder('asc')
                                        ->setAttributeFilter($attribute['attribute_id']) 
                                        ->setStoreFilter();
                                        //->load(); 
                
                $attributeOptionSingle->load();
              
                foreach($attributeOptionSingle as $value) {
                        $options[$value->getOptionId()] = $value->getValue();
                        $counter++;
                }
                
                if($this->_typeID == 2){    
                    $product = Mage::getModel('catalog/product'); 
                    $productCollection = $product->getCollection()
                                   ->addAttributeToSelect($this->_attribute[$attribute['attribute_id']]->getAttributeCode(), true) 
                                   //->addAttributeToSelect('status',true) 
                                   //->addAttributeToFilter("status",array("in"=>1))
                                   //->addAttributeToFilter("visibility",array("in"=>array(2,3,4)))
                                   //->addStoreFilter()
                                    ;    
                                    
                    $count = 0;
                   
                    $attribute_model = Mage::getModel('eav/entity_attribute')->load($this->_typeEntityID); 
                     
                    $default_option_id          = $this->_typeEntityValue;
                    $default_attribute_code     = $attribute_model->getAttributeCode(); 
                    $default_attribute_type     = $attribute_model->getFrontendInput(); 
                    $productCollection->addAttributeToSelect($default_attribute_code,true);  
                    
                    if($default_attribute_type == 'select'){        
                        $productCollection->addAttributeToFilter($default_attribute_code,$default_option_id);   
                    }
                    else if($default_attribute_type == 'multiselect'){
                        $productCollection->getSelect()
                            ->where("concat(',',_table_".$default_attribute_code.".value,',') like '%,".$default_option_id.",%'");  
                    }
                    //echo $productCollection->getSelect()->__toString();exit;
                      $selected_optionsstr = '';
                      foreach($productCollection->getData() as $data){
                        $selected_optionsstr .= ",".$data[$this->_attribute[$attribute['attribute_id']]->getAttributeCode()];        
                      }
                     
                      $selected_optionsarray = explode(",",$selected_optionsstr);
                    
                      foreach($options as $optionID=>$optionValue){  
                        if(!in_array($optionID,$selected_optionsarray)){  
                            unset($options[$optionID]);
                        }    
                      }
                }
                
                
              }
              else{
                    $attr_id = $this->getRequest()->getParam('attrid_'.$attribute['sort_order']);
                    if (preg_match("([^0-9])", $attr_id) >0) {
                        //illegal char found.
                        exit;
                    }
        
                    if($attr_id){
                       
                        $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder('asc')
                                        ->setAttributeFilter($attribute['attribute_id']) 
                                        ->setStoreFilter()
                                        ->load();                                        
              
                      foreach($attributeOptionSingle as $value) {
                            $options[$value->getOptionId()] = $value->getValue();
                            $counter++;
                      }
                      /*echo "<pre>";  
                      print_r($options);
                      echo "</pre>";*/
                      $product = Mage::getModel('catalog/product'); 
                      $productCollection = $product->getCollection()
                                   //->addAttributeToFilter('attribute_set_id',$this->_projectorEntityID)  
                                   ->addAttributeToSelect($this->_attribute[$attribute['attribute_id']]->getAttributeCode(), true)
                                   ->addAttributeToFilter("status",array("in"=>1))
                                   ->addAttributeToFilter("visibility",array("in"=>array(2,3,4))) 
                                   ->addStoreFilter()
                                    ;
                       
                       
                                    
                      $count = 0;
                     
                      while ($count < $attribute['sort_order']){
                            $request = $count + 1;
                            $option_id       = $this->getRequest()->getParam('attrid_'.$request);
                            if (preg_match("([^0-9])", $option_id) >0) {
                                //illegal char found.
                                exit;
                            }
                            $attribute_code     = $this->_attribute[$this->_attributeId[$count]]->getAttributeCode(); 
                            $attribute_type     = $this->_attribute[$this->_attributeId[$count]]->getFrontendInput(); 
                            $productCollection->addAttributeToSelect($attribute_code,true);  
                            //$this->_productCollection->addAttributeToFilter($attribute_code,array("like"=> "%{$option_id}%"));  
                            if($attribute_type == 'select'){        
                                $productCollection->addAttributeToFilter($attribute_code,$option_id);   
                            }
                            else if($attribute_type == 'multiselect'){
                                $productCollection->getSelect()
                                    ->where("concat(',',_table_".$attribute_code.".value,',') like '%,".$option_id.",%'");  
                            }
                            $count++;  
                      }
                     
                      $selected_optionsstr = '';
                      
                      
                      if($this->_typeID == 2){   
                      
                            $attribute_model = Mage::getModel('eav/entity_attribute')->load($this->_typeEntityID); 
                     
                            $default_option_id          = $this->_typeEntityValue;
                            $default_attribute_code     = $attribute_model->getAttributeCode(); 
                            $default_attribute_type     = $attribute_model->getFrontendInput(); 
                            //$productCollection->addAttributeToSelect($default_attribute_code,true);  
                    
                            if($default_attribute_type == 'select'){        
                                $productCollection->addAttributeToFilter($default_attribute_code,$default_option_id);   
                            }
                            else if($default_attribute_type == 'multiselect'){
                                $productCollection->getSelect()
                                    ->where("concat(',',_table_".$default_attribute_code.".value,',') like '%,".$default_option_id.",%'");  
                            }
                      }
                      //echo $productCollection->getSelect()->__toString();
                      foreach($productCollection->getData() as $data){
                        $selected_optionsstr .= ",".$data[$this->_attribute[$attribute['attribute_id']]->getAttributeCode()];        
                      }
                      
                      $selected_optionsarray = explode(",",$selected_optionsstr);
                      //echo "<pre>";print_r($selected_optionsarray);
                      //exit;
                      foreach($options as $optionID=>$optionValue){  
                        if(!in_array($optionID,$selected_optionsarray)){  
                            unset($options[$optionID]);
                        }    
                      } 
                     
                        if(count($options) == 0){
                            Mage::register("show_search_results","1");
                        }
                       
                    }
              }
            
            return $options;
        }
    }
  
    public function getLoadedProductCollection(){
        return $this->_productCollection;
    } 
    
    public function getattributeLabel($attribute = array()){
      
        if(array_key_exists('attribute_id',$attribute)){  
            $attribute_text   = $this->_attribute[$attribute['attribute_id']]->getFrontendLabel();
            return $attribute_text;
        }    
        else{
            return '';
        }
    }
    
    public function getRedirectUrl($entity_id,$sort_order){
        $redirecturl = ''; 
        //$redirecturl .= $this->helper('customsearch')->getProjectorSearchUrl()."index";
        
        $currentUrl = parse_url($this->helper('core/url')->getCurrentUrl(), PHP_URL_PATH);
        
        if(isset($_GET['cat'])){
            $redirecturl .= '?cat='.$_GET['cat'];    
        }
        
        $counter = 1;
        while ($counter <= $sort_order){
            
            if($redirecturl == '')
                $redirecturl .= '?';
            else
               $redirecturl .= '&';      
            
            if($sort_order == $counter)
                $redirecturl .= "attrid_".$counter."=".$entity_id;  
            else
                $redirecturl .= "attrid_".$counter."=".$this->getRequest()->getParam('attrid_'.$counter);   
            $counter++;  
        }
        
        return $currentUrl.$redirecturl;   
    }
    
    
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
    
    protected function _beforeToHtml()
    {   
        if($this->getRequest()->getParam('attrid_'.count($this->_searchAttributes)))
        {  
            $toolbar = $this->getToolbarBlock();
            
            $collection = $this->_getProductCollection();
        
            if ($orders = $this->getAvailableOrders()) {
                $toolbar->setAvailableOrders($orders);
            }
            if ($sort = $this->getSortBy()) {
                $toolbar->setDefaultOrder($sort);
            }
            if ($modes = $this->getModes()) {
                $toolbar->setModes($modes);
            }

            $toolbar->setCollection($collection);
            
            $this->setChild('toolbar', $toolbar);
            Mage::dispatchEvent('catalog_block_product_list_collection', array(
                'collection'=>$this->_getProductCollection(),
            ));

            $this->_getProductCollection()->load();
            Mage::getModel('review/review')->appendSummary($this->_getProductCollection());
            return parent::_beforeToHtml();
        }
    }
    
    public function _getProductCollection()
    {
        $count = 0; 
        if($this->getRequest()->getParam('attrid_'.count($this->_searchAttributes)) && is_null($this->_productCollection) ){ 
    
            $product = Mage::getModel('catalog/product'); 
            $this->_productCollection = $product->getCollection()
                                               //->addAttributeToFilter('attribute_set_id',$this->_projectorEntityID) 
                                               ->addAttributeToFilter("status",array("in"=>1))
                                               ->addAttributeToFilter("visibility",array("in"=>array(2,3,4)))
                                               ->addAttributeToSelect("*")
                                               ->addStoreFilter();
                                               //->load();
                                               
            if($this->_typeID == 2){   
                      
                $attribute_model = Mage::getModel('eav/entity_attribute')->load($this->_typeEntityID); 
         
                $default_option_id          = $this->_typeEntityValue;
                $default_attribute_code     = $attribute_model->getAttributeCode(); 
                $default_attribute_type     = $attribute_model->getFrontendInput(); 
                //$productCollection->addAttributeToSelect($default_attribute_code,true);  
        
                if($default_attribute_type == 'select'){        
                    $this->_productCollection->addAttributeToFilter($default_attribute_code,$default_option_id);   
                }
                else if($default_attribute_type == 'multiselect'){
                    $this->_productCollection->getSelect()
                        ->where("concat(',',_table_".$default_attribute_code.".value,',') like '%,".$default_option_id.",%'");  
                }
          }                                   
                                               
            while($count < count($this->_searchAttributes)){
                $option_id           = $this->getRequest()->getParam('attrid_'.($count+1));
                if (preg_match("([^0-9])", $option_id) >0) {
                     //illegal char found.
                     exit;
                }
                $attribute_code      = $this->_attribute[$this->_attributeId[$count]]->getAttributeCode(); 
                $attribute_type      = $this->_attribute[$this->_attributeId[$count]]->getFrontendInput(); 
                
                
                if($attribute_type == 'select'){        
                    $this->_productCollection->addAttributeToFilter($attribute_code,$option_id);   
                }
                else if($attribute_type == 'multiselect'){ 
                    $this->_productCollection->addAttributeToSelect($attribute_code,true);       
                    $this->_productCollection->getSelect()
                                       ->where("CONCAT(',',_table_".$attribute_code.".value,',') like '%,".$option_id.",%'");
                }    
                $count++;   
            }
            
            if ((preg_match("([^0-9])", $this->getRequest()->getParam('p')) >0) 
                    ||(preg_match("([^0-9])", $this->getRequest()->getParam('limit')) >0)
                    ) {
                //illegal char found.
                exit;
            }

        
        
            $currentPage = (int) $this->getRequest()->getParam('p', 1);
            $orderby     = $this->getRequest()->getParam('order');
            $dir         = $this->getRequest()->getParam('dir'); 
            $pageSize = (int) $this->getRequest()->getParam('limit',  Mage::getStoreConfig('catalog/frontend/list_per_page'));
           

            $originalParam = str_replace(' ','', $dir);
            $checkParam = filter_var(rawurldecode($originalParam), FILTER_SANITIZE_STRING);
            if ($originalParam != $checkParam){
                exit;
            }            
            $originalParam = str_replace(' ','', $orderby);
            $checkParam = filter_var(rawurldecode($originalParam), FILTER_SANITIZE_STRING);
            if ($originalParam != $checkParam){
                exit;
            }
            
            $this->_productCollection->addAttributeToSort($orderby,$dir);
            $this->_productCollection->setPage($currentPage, $pageSize);
            
        }
        return $this->_productCollection;  
    } 
   
}