<?php

class Ebulb_Customsearch_Model_Mysql4_Searchattributeentity extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    { 
        $this->_init('customsearch/searchattributeentity', 'entity_id');
        
        $this->_searchattentityint           = $this->getTable('customsearch/searchattributeentityint');
    }
    
    public function saveSearchAttributes($attributes,$attributes_sort_order,$entity_id){
      
        if($entity_id)
            $this->_getWriteAdapter()->delete($this->_searchattentityint, " parent_id = ".$entity_id); 
        
        foreach($attributes as $key=>$val){
             
            $data['parent_id'] = $entity_id;
            $data['attribute_id'] = $val;
            $data['sort_order'] = $attributes_sort_order[$val];
           
            if(!in_array($val,$attributesData)){ 
                $this->_getWriteAdapter()->insert($this->_searchattentityint, $data); 
            } 
        }
    }
    
    public function getSearchAttributes($entity_id){
        
        $select = $this->_getReadAdapter()->select()
                ->from(array('attributes'=>$this->_searchattentityint),array('entity_id','attribute_id','sort_order'))
                ->where('attributes.parent_id = '.$entity_id)
                ->order('attributes.sort_order');
                
        $attributes = $this->_getReadAdapter()->fetchAll($select);

       return $attributes;
        
    }
    
    public function getProjectorSearchEntityID(){
        $select = $this->_getReadAdapter()->select()
                ->from(array('attributeset'=>'eav_attribute_set'),array('attribute_set_id'))
                ->where('attributeset.entity_type_id = 4')
                ->where('attributeset.attribute_set_name = "Projector Lamps"');
    
        $attributeset = $this->_getReadAdapter()->fetchAll($select);
        $attributesetid = $attributeset[0]['attribute_set_id'];
        
        return $attributesetid;
    }
    
    public function loadbyTypeEntityid($type_entity_id = null){
      
        $select = $this->_getReadAdapter()->select()   
                  ->from(array('attributeentity'=>'search_attribute_entity'),array('entity_id','type_id','type_entity_id','type_label'));
             
        if(isset($type_entity_id))       
            $select->where('attributeentity.type_entity_id = '.$type_entity_id);    
                  
        $data = $this->_getReadAdapter()->fetchAll($select); 
       
        if(count($data)>0) 
            return $data[0]['entity_id'];     
        else 
            return '';
    }
    
    public function checkExistingRecord($entity_id=null,$type_id,$type_entity_id){
      
        $select = $this->_getReadAdapter()->select()   
            ->from(array('attributeentity'=>'search_attribute_entity'),array('entity_id'))
            ->where("attributeentity.type_id = ".$type_id)
            ->where("attributeentity.type_entity_id = ".$type_entity_id);
        
        if($entity_id)  
            $select->where("attributeentity.entity_id != ".$entity_id);
        
        $data = $this->_getReadAdapter()->fetchAll($select); 
       
        if(count($data)>0) 
            return true;        
        else
            return false;
    }
    
    public function getCurrentSearch($Category_id = null){
        
        
        
        $select = $this->_getReadAdapter()->select()   
            ->from(array('attributeentity'=>'search_attribute_entity'),array('entity_id','type_id','category_id','type_entity_id','type_entity_value'))
            ->where("attributeentity.category_id = ".$Category_id)
            ->where("attributeentity.enabled = 1");
        
        $data = $this->_getReadAdapter()->fetchAll($select); 
       
        if(count($data)>0){  
            return $data[0];        
        }
        else{    
            return '';
        }
    }
} 
