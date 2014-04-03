<?php

class Ebulb_Automaticfeed_Model_Mysql4_Autofeedinfo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('automaticfeed/autofeedinfo', 'feed_id');
        
        $this->_attributeTable                = $this->getTable('automaticfeed/autofeedattrinfo');  
        $this->_autofeedinfo                = $this->getTable('automaticfeed/autofeedinfo');  
        
    }
    
    public function saveAttributes($company_attr,$attribute_id,$custom_value,$sort_order,$modelid){
        
       if(is_array($company_attr)) {
        
           $this->_getWriteAdapter()->delete($this->_attributeTable, "feed_id={$modelid}");    
           
            foreach($company_attr as $key=>$val){
                 
                $data['feed_id'] = $modelid;
                $data['company_attr'] = $company_attr[$key];
                $data['attribute_id'] = $attribute_id[$key];
                $data['custom_value'] = $custom_value[$key];
                $data['sort_order']   = $sort_order[$key];
                  
                $this->_getWriteAdapter()->insert($this->_attributeTable, $data); 
                
            }    
       } 
    }
    
    public function getAttributes($feed_id){
        if($feed_id > 0 ){
            
            $select = $this->_getReadAdapter()->select()   
            ->from(array('autofeedattr'=>$this->_attributeTable),array('feed_id','company_attr','attribute_id','custom_value','sort_order'))
            ->where("autofeedattr.feed_id = ".$feed_id)
            ->order("autofeedattr.sort_order");
            
            $data = $this->_getReadAdapter()->fetchAll($select);
            
            return $data;
        }
        else return '';
    }
    
    public function getcompanyinfo($companyList='',$websiteList=''){   
        
        $select = $this->_getReadAdapter()->select()   
            ->from(array('autofeedinfo'=>$this->_autofeedinfo),array('*'));
            
        if($companyList != '')
            $select->where("autofeedinfo.company_id in (".$companyList.")");
        
        if($websiteList != '')
            $select->where("autofeedinfo.store_id in (".$websiteList.")");
       
        $data = $this->_getReadAdapter()->fetchAll($select);
            
        return $data;    
    }
} 