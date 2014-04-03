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
class Ebulb_Newreports_Model_Mysql4_Report_Order_Product_Updateat_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{

    protected $_periodFormat;
    protected $_inited = false;
    protected $_selectedColumns = array(
        /*'orders_count'              => 'COUNT(e.entity_id)',
        'total_qty_ordered'         => 'SUM(e.total_qty_ordered)',
        'base_profit_amount'        => 'SUM(IFNULL(e.base_subtotal_invoiced, 0) * e.base_to_global_rate) + SUM(IFNULL(e.base_discount_refunded, 0) * e.base_to_global_rate) - SUM(IFNULL(e.base_subtotal_refunded, 0) * e.base_to_global_rate) - SUM(IFNULL(e.base_discount_invoiced, 0) * e.base_to_global_rate) - SUM(IFNULL(e.base_total_invoiced_cost, 0) * e.base_to_global_rate)',
        'base_subtotal_amount'      => 'SUM(e.base_subtotal * e.base_to_global_rate)',
        'base_tax_amount'           => 'SUM(e.base_tax_amount * e.base_to_global_rate)',
        'base_shipping_amount'      => 'SUM(e.base_shipping_amount * e.base_to_global_rate)',
        'base_discount_amount'      => 'SUM(e.base_discount_amount * e.base_to_global_rate)',
        'base_grand_total_amount'   => 'SUM(e.base_grand_total * e.base_to_global_rate)',
        'base_invoiced_amount'      => 'SUM(e.base_total_paid * e.base_to_global_rate)',
        'base_refunded_amount'      => 'SUM(e.base_total_refunded * e.base_to_global_rate)',
        'base_canceled_amount'      => 'SUM(IFNULL(e.subtotal_canceled, 0) * e.base_to_global_rate)'*/
       
        'entity_id'                 =>  '(product.entity_id)',
        'order_date'                =>  '(e.updated_at)',  
        'product_sku'               =>  '(item.sku)',  
        'product_name'              =>  '(item.name)',  
        'sum_qty'                   =>  '( SUM(item.qty_ordered) -  SUM(item.qty_refunded) - SUM(item.qty_canceled) )', 
        //'sum_qty'                   =>  '( SUM(item.qty_ordered) )',
        'product_sellprice'         =>  '( item.base_price )',   
        //'product_sellprice_subtotal'=>  '( (item.base_price) * (SUM( item.qty_ordered )) )',  
        'product_sellprice_subtotal'=>  '( (item.base_price) * ( SUM(item.qty_ordered) -  SUM(item.qty_refunded) - SUM(item.qty_canceled) ) )',    
        'available_qty'             =>  'stock_item.qty',  
        //'product_cost'              =>  '( item.base_cost )',
       
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
            $this->getSelect()->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('store_id IN(?)', $storeIds);
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
        if (!$this->isTotals()) {
            /*if ('month' == $this->_period) {
                $this->_periodFormat = 'DATE_FORMAT(e.created_at, \'%Y-%m\')';
            } elseif ('year' == $this->_period) {
                $this->_periodFormat = 'EXTRACT(YEAR FROM e.created_at)';
            } else {
                $this->_periodFormat = 'DATE(e.created_at)';
            }
            $this->_selectedColumns += array('period' => $this->_periodFormat);*/
        }
        else{ 
            //$this->_selectedColumns['product_sellprice']  = '(SUM(item.base_price))'; 
            //$this->_selectedColumns['product_sellprice_subtotal']  = '(SUM(item.base_price * item.qty_ordered))';
            //$this->_selectedColumns['product_cost']               =  '(SUM(item.base_cost))';         
           // echo "<pre>";print_r($this->_selectedColumns);exit;
           
           $this->_selectedColumns['order_state']  = 'e.state';  
        }
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
       
        //echo Mage::registry('report_filter_product_sku');exit;
        
        // START COUNTER
        $sql = 'SET @N = 0;';
        $data = mage::getResourceModel('sales/order_item_collection')->getConnection()->query($sql);        
        
        $columns = $this->_getSelectedColumns();

        $mainTable = $this->getResource()->getMainTable(); 
        
         if(Mage::app()->getRequest()->getParam('sort'))
            $sortby  = Mage::app()->getRequest()->getParam('sort');
        else
            $sortby  = '(SUM( item.qty_ordered ))';
            
        if(Mage::app()->getRequest()->getParam('dir'))
            $dir     = Mage::app()->getRequest()->getParam('dir'); 
        else
            $dir     = 'desc';
        
        $subQuerycost = clone $this->getSelect();  
        $subQuerycost->from(array('xsov1' => 'catalog_product_entity_decimal'), array('average_cost'=>'xsov1.value')) 
                    ->joinInner(array('eav1'=>'eav_attribute'),'xsov1.attribute_id = eav1.attribute_id', array())
                    ->where('xsov1.entity_id = item.product_id')
                    ->where("eav1.attribute_code = 'average_cost'")
                    ;
        
        if (!is_null($this->_from) || !is_null($this->_to)) {
            $where = (!is_null($this->_from)) ? "e.updated_at >= '{$this->_from}'" : '';
            if (!is_null($this->_to)) {
                $where .= (!empty($where)) ? " AND e.updated_at <= '{$this->_to}'" : "e.updated_at <= '{$this->_to}'";
            }

            /*$subQuery = clone $this->getSelect();
            $subQuery->from(array('so' => $mainTable), array('DISTINCT DATE(so.updated_at)'))
                ->where($where);*/
        }
          
        $select = $this->getSelect()
            //->from(array('e' => $mainTable), $columns)
            ->from(array('product' => 'catalog_product_entity'), $columns)
            ->join(array('item'=>'sales_flat_order_item'),"item.product_id = product.entity_id ",array())
            ->join(array('e' => $mainTable),"e.entity_id = item.order_id ",array())
            ->join(array('stock_item' => 'cataloginventory_stock_item'),"product.entity_id = stock_item.product_id ",array())
            ->columns("(@N := @N +1) as counter")
            ->columns("(  ".new Zend_Db_Expr($subQuerycost) . " ) as product_cost") 
            /*->where('e.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ))*/
            ->where("e.status in ('complete','processing')")
            ->where("item.parent_item_id IS NULL") 
            ->where($where)
            ->order($sortby." ".$dir)
            //->group('product.entity_id') 
            ;
     
        if(Mage::registry('report_filter_product_sku')){
            $select->where(" item.sku like '%".Mage::registry('report_filter_product_sku')."%' ");     
        }
        
        if(Mage::registry('report_filter_product_name')){
            $select->where(" item.name like '%".Mage::registry('report_filter_product_name')."%' ");     
        }
        
        if(Mage::registry('report_filter_manufacturer')){  
            $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');    
            $select->join(array('product_entity_int'=>'catalog_product_entity_int'),"product_entity_int.entity_id = product.entity_id",array())
                   ->where('product_entity_int.attribute_id = '.$attribute->getData('attribute_id'))
                   ->where('product_entity_int.value = '.Mage::registry('report_filter_manufacturer')) 
                    ;    
        }
        
        if(Mage::registry('report_filter_vendor')){   
            $select->join(array('po_vendor_product'=>'po_vendor_product'),"po_vendor_product.product_id = product.entity_id",array())
                   ->where('po_vendor_product.vendor_id = '.Mage::registry('report_filter_vendor')) 
                    ;    
        }
        
        $this->_applyStoresFilter();
         
        $this->_applyOrderStatusFilter();
       
        /*if (!is_null($this->_from) || !is_null($this->_to)) {
            $select->where("DATE(e.updated_at) IN(?)", new Zend_Db_Expr($subQuery));
        } */
        
        if (!$this->isTotals()) {
            $select->group('product.entity_id');   
        } 

        //echo "<pre>";print_r($select);
        
        //echo $select->__toString()."<br />";
        //exit; */
        
        $this->_inited = true;
        
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
