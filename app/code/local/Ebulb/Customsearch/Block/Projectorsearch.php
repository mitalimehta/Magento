<?php 

class Ebulb_Customsearch_Block_Projectorsearch extends Mage_Catalog_Block_Product_List{   
    
    public $_projectorSearchID = null;
    public $_projectorEntityID = null;
    public $_searchAttributes = array();
    
    public $_attribute = array();
    public $_attributeId = array();
    
    public $_productCollection = null;
    
    public function _construct()
    {   
        $this->_projectorEntityID  = Mage::getModel('customsearch/searchattributeentity')->getProjectorSearchEntityID();
        
        if($this->_projectorEntityID)
            $this->_projectorSearchID  = Mage::getModel('customsearch/searchattributeentity')->loadbyTypeEntityid($this->_projectorEntityID);
        
        if($this->_projectorSearchID)
            $this->_searchAttributes   = Mage::getModel('customsearch/searchattributeentity')->getSearchAttributes($this->_projectorSearchID);
        
        foreach($this->_searchAttributes as $searchattribute){ 
            $this->_attribute[$searchattribute['attribute_id']] = Mage::getModel('eav/entity_attribute')->load($searchattribute['attribute_id']);
            $this->_attributeId[$searchattribute['sort_order']] = $searchattribute['attribute_id'];    
        } 
       
    }
    
    public function getAction()
    {
        return Mage::getUrl('customsearch/projectorsearchresult');
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
              
              if($attribute['sort_order'] == 0){                             
                $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder('asc')
                                        ->setAttributeFilter($attribute['attribute_id']) 
                                        ->setStoreFilter()
                                        ->load(); 
                                        
              
                  foreach($attributeOptionSingle as $value) {
                        $options[$value->getOptionId()] = $value->getValue();
                        $counter++;
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
                      
                      $product = Mage::getModel('catalog/product'); 
                      $productCollection = $product->getCollection()
                                   ->addAttributeToFilter('attribute_set_id',$this->_projectorEntityID)  
                                   ->addAttributeToSelect($this->_attribute[$attribute['attribute_id']]->getAttributeCode(), true) 
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
                      //echo $this->_productCollection->getSelect()->__toString()."<br />";exit;
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
        $redirecturl .= $this->helper('customsearch')->getProjectorSearchUrl()."index";
        
        $counter = 1;
        while ($counter <= $sort_order){
            if($sort_order == $counter)
                $redirecturl .= "/attrid_".$counter."/".$entity_id;  
            else
                $redirecturl .= "/attrid_".$counter."/".$this->getRequest()->getParam('attrid_'.$counter);   
            $counter++;  
        }
        
        return $redirecturl;   
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
                                               ->addAttributeToFilter('attribute_set_id',$this->_projectorEntityID) 
                                               ->addAttributeToFilter("status",array("in"=>1))
                                               ->addAttributeToFilter("visibility",array("in"=>array(2,3,4)))
                                               ->addAttributeToSelect("*")
                                               ->addStoreFilter();
                                               //->load();
                                               
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