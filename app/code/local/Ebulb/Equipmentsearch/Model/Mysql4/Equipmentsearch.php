<?php

class Ebulb_Equipmentsearch_Model_Mysql4_Equipmentsearch extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_equipmentTable;
    protected $_equipmentcategoryTable;
    protected $_equipmentcategoryrelationTable;
    protected $_equipmentcategorywebsiteTable;
    protected $_equipmentmanufTable;
    protected $_equipmentproductTable;
    
    protected $_storeid;
    
    public function _construct()
    {    
        $this->_init('equipmentsearch/equipmentsearch','equipment_id');
        
        $resources = Mage::getSingleton('core/resource');   
        
        $this->_equipmentTable                     = $resources->getTableName('equipmentsearch/equipment');
        $this->_equipmentcategoryTable             = $resources->getTableName('equipmentsearch/equipment_category');
        $this->_equipmentcategoryrelationTable     = $resources->getTableName('equipmentsearch/equipment_category_relation');
        $this->_equipmentcategorywebsiteTable      = $resources->getTableName('equipmentsearch/equipment_category_website');
        $this->_equipmentmanufTable                = $resources->getTableName('equipmentsearch/equipment_manuf');
        $this->_equipmentproductTable              = $resources->getTableName('equipmentsearch/equipment_product');
        
        $this->_storeid = Mage::app()->getStore()->getId();
    }
    
    
    public function getDeviceTypeCollection(){
        
        /*select ec.category_id  as category_id,ec.name as name  
        from `equipment_category` as ec
        left join `equipment_category_website` ecw on ec.category_id= ecw.category_id
        left join `equipment_category_relation` ecr on ec.category_id = ecr.category_id
        ,`equipment` e left join `equipment_category_relation` ecr1 on e.equipment_id = ecr1.equipment_id
        where ecw.store_id = 1
        group by category_id
        order by name*/
        
       $select = $this->_getReadAdapter()->select()
            ->from(array('ec'=>$this->_equipmentcategoryTable), array('category_id'=>'ec.category_id','name'=>'ec.name'))
            ->join(array('ecw'=>$this->_equipmentcategorywebsiteTable), 'ec.category_id= ecw.category_id',array('category_id'=>'ec.category_id','name'=>'ec.name'))
            ->join(array('ecr'=>$this->_equipmentcategoryrelationTable), 'ec.category_id = ecr.category_id and e.equipment_id = ecr.equipment_id',array('category_id'=>'ec.category_id','name'=>'ec.name'))
            ->from(array('e'=>$this->_equipmentTable), array('category_id'=>'ec.category_id','name'=>'ec.name'))  
            //->join(array('ecr1'=>$this->_equipmentcategoryrelationTable), 'e.equipment_id = ecr1.equipment_id',array('category_id'=>'ec.category_id','name'=>'ec.name'))
            ->where('ecw.store_id = '.$this->_storeid)
            ->order('ec.name ASC')
            ->group('ec.category_id');
     
       $devices = $this->_getReadAdapter()->fetchAll($select);
     
       return $devices; 
    
    }
    
    public function getManufCollection($deviceid = ''){

        /*select ecm.manuf_id as manuf_id,ecm.name as name from `equipment` ec,`equipment_manuf` ecm , `equipment_category_relation` r
        where ec.equipment_id=r.equipment_id 
        and ecm.manuf_id = ec.manuf_id 
        and r.category_id  = 1
        group by manuf_id
        order by name*/
        
        $select = $this->_getReadAdapter()->select() 
                  ->from(array('ec'=>$this->_equipmentTable), array('manuf_id'=>'ecm.manuf_id','name'=>'ecm.name'))
                  ->from(array('ecm'=>$this->_equipmentmanufTable), array('manuf_id'=>'ecm.manuf_id','name'=>'ecm.name'))
                  ->from(array('ecr'=>$this->_equipmentcategoryrelationTable), array('manuf_id'=>'ecm.manuf_id','name'=>'ecm.name'))
                  ->from(array('ecp'=>$this->_equipmentproductTable,array('manuf_id'=>'ecm.manuf_id','name'=>'ecm.name'))) 
                  ->where('ecm.manuf_id = ec.manuf_id')
                  ->where('ec.equipment_id=ecr.equipment_id')
                  ->where('ecp.equipment_id=ec.equipment_id')
                  ->where('ecr.category_id = '.$deviceid)
                  ->order('ecm.name ASC')
                  ->group('ecm.manuf_id');
                 
       $devices = $this->_getReadAdapter()->fetchAll($select);
     
       return $devices; 
        
    }
    
    public function getEquipmentCollection($deviceid='',$manufid=''){
        
        /*select e.equipment_id as equipment_id ,e.name as name 
        from `equipment` e,`equipment_manuf` ecm,`equipment_category_relation` ecr,`equipment_category` ec#,`equipment_product` ecp
        where e.manuf_id = ecm.manuf_id
        and ecm.manuf_id = 336
        and ec.category_id = ecr.category_id
        and ec.category_id = 1
        and ecr.equipment_id = e.equipment_id
        #and e.equipment_id = ecp.equipment_id
        group by equipment_id
        order by name */
        
        $select = $this->_getReadAdapter()->select()
                    ->from(array('ecp'=>$this->_equipmentproductTable,array('equipment_id'=>'e.equipment_id','e.name'=>'name'))) 
                    ->from(array('ec'=>$this->_equipmentcategoryTable,array('equipment_id'=>'e.equipment_id','e.name'=>'name'))) 
                    ->from(array('ecm'=>$this->_equipmentmanufTable,array('equipment_id'=>'e.equipment_id','e.name'=>'name')))
                    ->from(array('ecr'=>$this->_equipmentcategoryrelationTable,array('equipment_id'=>'e.equipment_id','e.name'=>'name')))  
                    ->from(array('e'=>$this->_equipmentTable,array('equipment_id'=>'e.equipment_id','e.name'=>'name'))) 
                    ->where('e.manuf_id = ecm.manuf_id')
                    ->where('ecm.manuf_id = '.$manufid)
                    ->where('ec.category_id = ecr.category_id')
                    ->where('ec.category_id = '.$deviceid)
                    ->where('ecr.equipment_id = e.equipment_id')
                    ->where('e.equipment_id = ecp.equipment_id')
                    ->order('e.name ASC')
                    ->group('e.equipment_id');
                                                 
        $equipments = $this->_getReadAdapter()->fetchAll($select);    
        
       return $equipments;   
    }
    
    public function getEquipmentDetails($equipment=''){
     
        $select = $this->_getReadAdapter()->select()
                    ->from(array('e'=>$this->_equipmentTable,array('equipment_id'=>'e.equipment_id','name'=>'e.name','description'=>'e.description','image'=>'e.image')))
                    ->where('e.equipment_id = '.$equipment);
        
        $equipmentdetail = $this->_getReadAdapter()->fetchAll($select);
       
       return $equipmentdetail;            
    } 
    
    public function getProductCollection($equipment=''){  
       $select = $this->_getReadAdapter()->select()
            ->from(array('product'=>$this->_equipmentproductTable),array('product_id'=>'product.product_id'))
            ->where('product.equipment_id = '.$equipment);
       
       $products = $this->_getReadAdapter()->fetchAll($select);

       return $products;
    }
    
    public function getdevicename($deviceid){
        $select = $this->_getReadAdapter()->select()
            ->from(array('ec'=>$this->_equipmentcategoryTable), array('name'=>'ec.name'))
            ->where('ec.category_id = '.$deviceid);
       
       $device = $this->_getReadAdapter()->fetchAll($select);
       
       return $device[0]['name'];    
    }
    
    
    
    public function getmanufname($manufid){  
        $select = $this->_getReadAdapter()->select()
            ->from(array('ecm'=>$this->_equipmentmanufTable,array('e.name'=>'name')))
            ->where('ecm.manuf_id = '.$manufid);
       
       $manuf = $this->_getReadAdapter()->fetchAll($select);
      
       return $manuf[0]['name'];    
    }
    
    
    
    
}