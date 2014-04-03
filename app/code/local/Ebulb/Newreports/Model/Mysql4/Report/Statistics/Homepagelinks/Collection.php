<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report order updated_at collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Newreports_Model_Mysql4_Report_Statistics_Homepagelinks_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{

    protected $_periodFormat;
    protected $_inited = false;
    protected $_selectedColumns = array(
        
        'entity_id'                 =>  '(p.entity_id)',
        'page_title'                  =>  '(p.page_title)',
        'page_link'                =>  '(p.html_link)',
        'total_count'              =>  '(count(c.link_id))',  
       
       
    );

    /**
     * Initialize custom resource model
     *
     * @param array $parameters
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order', 'entity_id');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Apply stores filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    protected function _applyStoresFilter()
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        } 

        if ($nullCheck) {  
            $this->getSelect()->where('c.website_id IN(?) OR website_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('c.website_id IN(?)', $storeIds);
        }
                      
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Retrieve array of columns to select
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    protected function _initSelect()
    { 
        if ($this->_inited) {
            return $this;
        }
        
        if(Mage::app()->getRequest()->getParam('sort'))
            $sortby  = Mage::app()->getRequest()->getParam('sort');
        else
            $sortby  = 'page_title';
            
        if(Mage::app()->getRequest()->getParam('dir'))
            $dir     = Mage::app()->getRequest()->getParam('dir'); 
        else
            $dir     = 'asc';        
         
        // START COUNTER
        $sql = 'SET @N = 0;';
        $data = mage::getResourceModel('sales/order_item_collection')->getConnection()->query($sql);        
        
        $columns = $this->_getSelectedColumns();

        $mainTable = $this->getResource()->getMainTable();   
        
        if (!is_null($this->_from) || !is_null($this->_to)) {
            $where = (!is_null($this->_from)) ? "c.datetime >= '{$this->_from}'" : '';
            if (!is_null($this->_to)) {
                $where .= (!empty($where)) ? " AND c.datetime <= '{$this->_to}'" : "c.datetime <= '{$this->_to}'";
            }

        
              
        $select = $this->getSelect() 
            ->from(array('p' => 'page_links'),$columns)
            ->joinLeft(array('c'=>'page_clicks'),'p.entity_id = c.link_id',array()) 
            ->columns("(@N := @N +1) as counter")    
            ->where($where)  
            ->order($sortby." ".$dir)
            ->group('p.entity_id')
            ;
       
        $this->_applyStoresFilter(); 
        $this->_inited = true;
        
        }
        else{
            $select = $this->getSelect() 
            ->from(array('p' => 'page_links'),'p.entity_id')
            ->limit(1)
            ;  
            $this->_applyStoresFilter();
         
            $this->_applyOrderStatusFilter(); 
            $this->_inited = true; 
           // echo $select->__toString();  
        }
        return $this;
    }
    
      
    /**
     * Load
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Sales_Model_Mysql4_Report_Order_Updatedat_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        $this->setApplyFilters(false);
        
        return parent::load($printQuery, $logQuery);
    }

}
