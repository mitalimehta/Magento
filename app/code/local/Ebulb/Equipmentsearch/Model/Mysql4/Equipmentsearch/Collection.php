<?php 

class Ebulb_Equipmentsearch_Model_Mysql4_Equipmentsearch_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_equipmentTable;
    protected $_equipmentcategoryTable;
    protected $_equipmentcategoryrelationTable;
    protected $_equipmentcategorywebsiteTable;
    protected $_equipmentmanufTable;
    protected $_equipmentproductTable;
    
    public function _construct()
    {
        parent::_construct();
        
        $resources = Mage::getSingleton('core/resource');
        
        $this->_init('equipmentsearch/equipmentsearch');
        
        $this->_equipmentTable                     = $resources->getTableName('equipmentsearch/equipment');
        $this->_equipmentcategoryTable             = $resources->getTableName('equipmentsearch/equipment_category');
        $this->_equipmentcategoryrelationTable     = $resources->getTableName('equipmentsearch/equipment_category_relation');
        $this->_equipmentcategorywebsiteTable      = $resources->getTableName('equipmentsearch/equipment_category_website');
        $this->_equipmentmanufTable                = $resources->getTableName('equipmentsearch/equipment_manuf');
        $this->_equipmentproductTable              = $resources->getTableName('equipmentsearch/equipment_product');
        
    }
    
   
    
}