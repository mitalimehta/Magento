<?php

class Ebulb_Automativesearch_Model_Mysql4_Automativesearch extends Mage_Core_Model_Mysql4_Abstract
{
 
    protected $_productTable;
    
    public function _construct()
    {    
        $this->_init('automativesearch/automativesearch','car_manufacturer_product_id');
        
        $this->_productTable                        = $this->getTable('automativesearch/automotive_product'); 
    }
    
  
    public function getManufacturerCollection(){
       
      
       $select_attribute = $this->_getReadAdapter()->select()
                        ->from(array('attribute'=>'eav_attribute'),array('attribute_id'))
                        ->where(" attribute.attribute_code = 'car_manufacturer'")
                        ->where(" attribute.entity_type_id = 4")
                        ;
       
       $attribute_id = $this->_getReadAdapter()->fetchOne($select_attribute);  
      
       $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder('asc')
                                        ->setAttributeFilter($attribute_id)
                                        ->setStoreFilter();  
                
       $attributeOptionSingle->load();
                
       $car_manufacturers = $attributeOptionSingle->getData();
       return $car_manufacturers;      
      
    }
    
    public function getYearCollection($manufacturerid = ''){
        
        $select_attribute = $this->_getReadAdapter()->select()
                        ->from(array('attribute'=>'eav_attribute'),array('attribute_id'))
                        ->where(" attribute.attribute_code = 'car_year'")
                        ->where(" attribute.entity_type_id = 4")
                        ;
       
       $attribute_id = $this->_getReadAdapter()->fetchOne($select_attribute);  
      
       $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder(' desc','value')
                                        ->setAttributeFilter($attribute_id)
                                        ->setStoreFilter();  
      
       $attributeOptionSingle->load();
       $car_years = $attributeOptionSingle->getData();  
      
       $select_year = $this->_getReadAdapter()->select()
                        ->from(array('product'=>$this->_productTable),array('car_manufacturer_year_id'=>'car_manufacturer_year_id'))
                        ->where(" product.car_manufacturer_id = ".$manufacturerid)
                        ->distinct(true)
                        ;
       
       $year_options = $this->_getReadAdapter()->fetchAll($select_year); 
       $year_options_array = array();                   
       foreach($year_options as $key=>$val){
            $year_options_array[$key] = $val['car_manufacturer_year_id'];    
       }
       
       foreach($car_years as $key=>$val){
           if(!in_array($val['option_id'] ,$year_options_array)){
               unset($car_years[$key]);
           }
       }
       
       return $car_years;
    }
    
    public function getModelCollection($manufacturerid='',$yearid=''){
        
        $select_attribute = $this->_getReadAdapter()->select()
                        ->from(array('attribute'=>'eav_attribute'),array('attribute_id'))
                        ->where(" attribute.attribute_code = 'car_model'")
                        ->where(" attribute.entity_type_id = 4")
                        ;
       
       $attribute_id = $this->_getReadAdapter()->fetchOne($select_attribute);  
      
       $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder(' desc','value')
                                        ->setAttributeFilter($attribute_id)
                                        ->setStoreFilter();  
      
       $attributeOptionSingle->load();
       $car_models = $attributeOptionSingle->getData();  
       
       $select_model = $this->_getReadAdapter()->select()
                        ->from(array('product'=>$this->_productTable),array('car_manufacturer_model_id'=>'car_manufacturer_model_id'))
                        ->where(" product.car_manufacturer_id = ".$manufacturerid)
                        ->where(" product.car_manufacturer_year_id = ".$yearid)
                        ->distinct(true)
                        ;
      
       $model_options = $this->_getReadAdapter()->fetchAll($select_model); 
       $model_options_array = array();                   
       foreach($model_options as $key=>$val){
            $model_options_array[$key] = $val['car_manufacturer_model_id'];    
       }
      
       foreach($car_models as $key=>$val){
           if(!in_array($val['option_id'] ,$model_options_array)){
               unset($car_models[$key]);
           }
       }
      
       return $car_models;
    }
    
    public function getTypeCollection($manufacturerid='',$yearid='',$modelid=''){
        $select_attribute = $this->_getReadAdapter()->select()
                        ->from(array('attribute'=>'eav_attribute'),array('attribute_id'))
                        ->where(" attribute.attribute_code = 'car_type'")
                        ->where(" attribute.entity_type_id = 4")
                        ;
       
       $attribute_id = $this->_getReadAdapter()->fetchOne($select_attribute);  
      
       $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder(' desc','value')
                                        ->setAttributeFilter($attribute_id)
                                        ->setStoreFilter();  
      
       $attributeOptionSingle->load();
       $car_types = $attributeOptionSingle->getData();  
       
       $select_type = $this->_getReadAdapter()->select()
                        ->from(array('product'=>$this->_productTable),array('car_manufacturer_type_id'=>'car_manufacturer_type_id'))
                        ->where(" product.car_manufacturer_id = ".$manufacturerid)
                        ->where(" product.car_manufacturer_year_id = ".$yearid)
                        ->where(" product.car_manufacturer_model_id = ".$modelid)
                        ->distinct(true)
                        ;
      
       $type_options = $this->_getReadAdapter()->fetchAll($select_type); 
       $type_options_array = array();                   
       foreach($type_options as $key=>$val){
            $type_options_array[$key] = $val['car_manufacturer_type_id'];    
       }
      
       foreach($car_types as $key=>$val){
           if(!in_array($val['option_id'] ,$type_options_array)){
               unset($car_types[$key]);
           }
       }
      
       return $car_types;
    }
    
    public function getProductCollection($manufacturerid='',$yearid='',$modelid='', $typeid='',$locationid=''){
       
        $select = $this->_getReadAdapter()->select()
            ->from(array('product'=>$this->_productTable),array('product_id'=>'product.product_id'))
            //->join(array('manufacturer'=>$this->_carmanufacturerTable), 'manufacturer.car_manufacturer_id=product.car_manufacturer_id',array('product_id'=>'product.product_id'))
            //->join(array('year'=>$this->_carmanufactureryearTable),'year.car_manufacturer_year_id=product.car_manufacturer_year_id',array('product_id'=>'product.product_id'))
            //->join(array('model'=>$this->_carmanufacturermodelTable),'model.car_manufacturer_model_id=product.car_manufacturer_model_id',array('product_id'=>'product.product_id'))
            ->order('location_id ASC') ;
            
            //if($manufacturerid != ''){    
                $select->where("product.car_manufacturer_id = '".$manufacturerid."'");   
            //}
            //if($yearid != ''){    
                $select->where("product.car_manufacturer_year_id = '".$yearid."'");   
            //}
            //if($modelid != ''){    
                $select->where("product.car_manufacturer_model_id = '".$modelid."'");   
            //}
            if($typeid != ''){  
                //$select->join(array('type'=>$this->_carmanufacturertypeTable),'type.car_manufacturer_type_id=product.car_manufacturer_type_id',array('product_id'=>'product.product_id'));
                $select->where("product.car_manufacturer_type_id = ".$typeid);   
            }
            if($locationid != ''){
                $select->where("product.location_id = ".$locationid);    
            } 
           
            $products = $this->_getReadAdapter()->fetchAll($select);

       return $products;
    }
       
    public function getlocationCollection($manufacturerid='',$yearid='',$modelid='',$typeid=''){
       $select_attribute = $this->_getReadAdapter()->select()
                        ->from(array('attribute'=>'eav_attribute'),array('attribute_id'))
                        ->where(" attribute.attribute_code = 'car_bulb_location'")
                        ->where(" attribute.entity_type_id = 4")
                        ;
       
       $attribute_id = $this->_getReadAdapter()->fetchOne($select_attribute);  
      
       $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder(' desc','value')
                                        ->setAttributeFilter($attribute_id)
                                        ->setStoreFilter();  
      
       $attributeOptionSingle->load();
       $car_types = $attributeOptionSingle->getData();  
       
       $select_type = $this->_getReadAdapter()->select()
                        ->from(array('product'=>$this->_productTable),array('location_id'=>'location_id'))
                        ->where(" product.car_manufacturer_id = ".$manufacturerid)
                        ->where(" product.car_manufacturer_year_id = ".$yearid)
                        ->where(" product.car_manufacturer_model_id = ".$modelid)
                        ->distinct(true)
                        ;
       if($typeid)
            $select_type->where(" product.car_manufacturer_type_id = ".$typeid);  
            
       $type_options = $this->_getReadAdapter()->fetchAll($select_type); 
       $type_options_array = array();   
                       
       foreach($type_options as $key=>$val){
            $type_options_array[$key] = $val['location_id'];    
       }
      
       foreach($car_types as $key=>$val){
           if(!in_array($val['option_id'] ,$type_options_array)){
               unset($car_types[$key]);
           }
       }
      
       return $car_types;
    }
    
    public function getManufacturerName($manufacturerid){
        $select = $this->_getReadAdapter()->select()
                ->from(array('manufacturer'=>$this->_carmanufacturerTable),array('car_manufacturer_name'))
                ->where('manufacturer.car_manufacturer_id = '.$manufacturerid);
                
        $manufacturer = $this->_getReadAdapter()->fetchAll($select);

       return $manufacturer;         
    }
    
    public function getYearName($yearid){
        $select = $this->_getReadAdapter()->select()
                ->from(array('year'=>$this->_carmanufactureryearTable),array('car_manufacturer_year_value'))
                ->where('year.car_manufacturer_year_id = '.$yearid);
                
        $year = $this->_getReadAdapter()->fetchAll($select);

       return $year;         
    } 
    
    public function getModelName($modelid){
        $select = $this->_getReadAdapter()->select()
                ->from(array('model'=>$this->_carmanufacturermodelTable),array('car_manufacturer_model_name'))
                ->where('model.car_manufacturer_model_id = '.$modelid);
                
        $model = $this->_getReadAdapter()->fetchAll($select);

       return $model;         
    }
    
     public function getTypeName($typeid){
        $select = $this->_getReadAdapter()->select()
                ->from(array('type'=>$this->_carmanufacturertypeTable),array('car_manufacturer_type_name'))
                ->where('type.car_manufacturer_type_id = '.$typeid);
                
        $type = $this->_getReadAdapter()->fetchAll($select);

       return $type;         
    }
    
    public function getManufacturerIDFromName($manufacturerName){
        $manufacturerName = str_replace("-"," ",$manufacturerName);
        
        $select = $this->_getReadAdapter()->select()
                ->from(array('manufacturer'=>$this->_carmanufacturerTable),array('car_manufacturer_id'))
                ->where("manufacturer.car_manufacturer_name = '".$manufacturerName."'");
                
        $manufacturer = $this->_getReadAdapter()->fetchAll($select);
      
        return $manufacturer; 
                  
    }   
    
    public function getManufacturersByLetter($letter=''){
        if($letter)
        {  
               
                $select_attribute = $this->_getReadAdapter()->select()
                        ->from(array('attribute'=>'eav_attribute'),array('attribute_id'))
                        ->where(" attribute.attribute_code = 'car_manufacturer'")
                        ->where(" attribute.entity_type_id = 4")
                        ;
       
               $attribute_id = $this->_getReadAdapter()->fetchOne($select_attribute);  
              
               $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                                ->setPositionOrder('asc')
                                                ->setAttributeFilter($attribute_id)
                                                ->setStoreFilter();  
               
               if(strlen(trim($letter)) == 1){
                    $attributeOptionSingle->getSelect()->where("store_default_value.value LIKE '".$letter."%'");  
               }
               
               $attributeOptionSingle->load();
                        
               $car_manufacturers = $attributeOptionSingle->getData();
               return $car_manufacturers;  
        }
    }
    
      
    public function getAttrbiuteId($attribute_code='')
    {
         $select_attribute = $this->_getReadAdapter()->select()
                        ->from(array('attribute'=>'eav_attribute'),array('attribute_id'))
                        ->where(" attribute.attribute_code = '".$attribute_code."'")
                        ->where(" attribute.entity_type_id = 4")
                        ;
       
       $attribute_id = $this->_getReadAdapter()->fetchOne($select_attribute);
       return $attribute_id;
    }
    
    
    public function getAllOptions($attribute_code)
    {
       
       $attribute_id = $this->getAttrbiuteId($attribute_code);
       
       $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setPositionOrder('asc')
                                        ->setAttributeFilter($attribute_id)
                                        ->setStoreFilter();  
                
       $attributeOptionSingle->load();
       $car_manufacturers = array();
       foreach($attributeOptionSingle->getData() as $key=>$attribute){
            $car_manufacturers[$attribute['option_id']]    =  $attribute['value'];
       }         
       return $car_manufacturers;
    }
  
    public function saveautomotivebulbs($automotivebulbs)
    {
        $car_manufacturer_id          = $automotivebulbs['car_manufacturer_id'] ;
        $car_manufacturer_year_id     = $automotivebulbs['car_manufacturer_year_id'] ;
        $car_manufacturer_model_id    = $automotivebulbs['car_manufacturer_model_id'] ;
        $car_manufacturer_type_id     = $automotivebulbs['car_manufacturer_type_id'] ;
        
        if($car_manufacturer_id == '' || $car_manufacturer_year_id == '' || $car_manufacturer_model_id == ''){
            return 'Please select data';
        }
        
        if(!isset($automotivebulbs['entity_id']))
        {
            //check for exitsting combination first  i yes then show error message
            
            $select = $this->_getReadAdapter()->select()
                ->from(array('product'=>$this->_productTable),array('car_manufacturer_product_id'))
                ->where("product.car_manufacturer_id = '".$car_manufacturer_id."'")
                ->where("product.car_manufacturer_year_id = '".$car_manufacturer_year_id."'")
                ->where("product.car_manufacturer_model_id = '".$car_manufacturer_model_id."'")
                ;
                
            if($car_manufacturer_type_id != '')
                $select->where("product.car_manufacturer_type_id = '".$car_manufacturer_type_id."'");
                      
            $combinations = $this->_getReadAdapter()->fetchAll($select);
            
            if(count($combinations) > 0){
                return 'This combination is already existing. Please edit for same combination';
            }
        }
        
        if($car_manufacturer_type_id != '')  
            $this->_getWriteAdapter()->delete($this->_productTable, "car_manufacturer_id={$car_manufacturer_id} AND car_manufacturer_year_id={$car_manufacturer_year_id} AND car_manufacturer_model_id={$car_manufacturer_model_id} AND car_manufacturer_type_id={$car_manufacturer_type_id}");         
        else
            $this->_getWriteAdapter()->delete($this->_productTable, "car_manufacturer_id={$car_manufacturer_id} AND car_manufacturer_year_id={$car_manufacturer_year_id} AND car_manufacturer_model_id={$car_manufacturer_model_id}");         
        
        foreach($automotivebulbs as $key=>$val)
        {
                
                $data['car_manufacturer_id']        = $car_manufacturer_id;
                $data['car_manufacturer_year_id']   = $car_manufacturer_year_id;
                $data['car_manufacturer_model_id']  = $car_manufacturer_model_id;
                $data['car_manufacturer_type_id']   = $car_manufacturer_type_id;
                $data['location_id']                = $val['location_id'];
                
                foreach($val['product_id'] as $product){
                    
                    $product_model = Mage::getModel('catalog/product');   
                    
                    $productsku                     = trim($product);
                    
                    try{$product = $product_model->loadByAttribute('sku',$productsku);}catch(Exception $e){
                       // echo "*Invalid code:$productsku<br> ";
                       // return '*Invalid code:$productsku<br>';
                       continue;
                    }
                      
                      if($product) {
                        $product_id = $product->getId();    
                      }
                      else {
                        $product_id = '';
                      }
                       
                    $data['product_id']             = $product_id;
                        
                    if($product_id != '') 
                        $this->_getWriteAdapter()->insert($this->_productTable, $data); 
                         
                        $insert_id = $this->_getWriteAdapter()->lastInsertId();
                }  
                
        }
        return $insert_id;
    }
    
    function getAutomotiveBulbs($car_manufacturer_id,$car_manufacturer_year_id,$car_manufacturer_model_id,$car_manufacturer_type_id,$id=''){
        if($id!=''){ 
            $select = $this->_getReadAdapter()->select()
                ->from(array('product'=>$this->_productTable),array('*'))
                ->where("product.car_manufacturer_id = '".$car_manufacturer_id."'")
                ->where("product.car_manufacturer_year_id = '".$car_manufacturer_year_id."'")
                ->where("product.car_manufacturer_model_id = '".$car_manufacturer_model_id."'")
                ->order("product.location_id")
                ;
                
            if($car_manufacturer_type_id != '')
                $select->where("product.car_manufacturer_type_id = '".$car_manufacturer_type_id."'");
                      
            $combinations = $this->_getReadAdapter()->fetchAll($select);
            
            foreach($combinations as $key=>$value){
                $product_model = Mage::getModel('catalog/product');  
                $product_model->load($value['product_id']);
                $combinations[$key]['sku'] = $product_model->getSku();  
            }
            
            if(count($combinations) > 0)
                return $combinations;
        }
    }
    
    function deletecombination($car_manufacturer_id,$car_manufacturer_year_id,$car_manufacturer_model_id,$car_manufacturer_type_id,$id=''){
        if($id!=''){ 
            if($car_manufacturer_type_id != '')  
                $this->_getWriteAdapter()->delete($this->_productTable, "car_manufacturer_id={$car_manufacturer_id} AND car_manufacturer_year_id={$car_manufacturer_year_id} AND car_manufacturer_model_id={$car_manufacturer_model_id} AND car_manufacturer_type_id={$car_manufacturer_type_id}");         
            else
                $this->_getWriteAdapter()->delete($this->_productTable, "car_manufacturer_id={$car_manufacturer_id} AND car_manufacturer_year_id={$car_manufacturer_year_id} AND car_manufacturer_model_id={$car_manufacturer_model_id}");         
        
        }    
    }
}