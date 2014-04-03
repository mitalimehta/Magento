<?php

class Ebulb_Purchase_Model_Salesorderqty extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/salesorderqty');
    }
    
    private function getAllProductIds($productid){
        $allProductIds = array();

        $masterId = $productid;
        
        $query = 'select cpemaster.entity_id as masterid, cpev.entity_id as slaveid, cpemaster.sku
from `catalog_product_entity_varchar` as cpev use index(FK_CATALOG_PRODUCT_ENTITY_VARCHAR_ATTRIBUTE),
`catalog_product_entity` as cpemaster
 where cpev.attribute_id= 1485 
 and cpev.entity_id = ?
 and cpev.value = cpemaster.sku';
        $queryResult = Mage::getSingleton('core/resource')->getConnection('core_write')->query($query,array($masterId) );
        $masterProductIdResult = $queryResult->fetchAll();
        
        foreach($masterProductIdResult  as $row){
            $masterId = $row['masterid'];
            break;
        }
        
        $allProductIds[] = $masterId;

            $query2 = 'select cpemaster.entity_id as masterid, cpev.entity_id as slaveid, cpemaster.sku
from `catalog_product_entity_varchar` as cpev use index(FK_CATALOG_PRODUCT_ENTITY_VARCHAR_ATTRIBUTE),
`catalog_product_entity` as cpemaster
 where cpev.attribute_id= 1485 
 and cpemaster.entity_id = ?
 and cpev.value = cpemaster.sku';
            $queryResult = Mage::getSingleton('core/resource')->getConnection('core_write')->query($query2, array($masterId));
            $slaveProductIdResult = $queryResult->fetchAll();

            foreach ($slaveProductIdResult as $row) {
                $allProductIds[] = $row['slaveid'];
            }
        return $allProductIds;
        
    }
    public function getSalesOrderQty($productid=null)
    {
        $allQty = 0;

        $productids = $this->getAllProductIds($productid);
        
        foreach($productids as $tempProductId){
            $allQty += $this->getResource()->getSalesOrderQty($tempProductId);
        }
        return $allQty; 
    }
    
}
