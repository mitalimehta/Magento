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
class Ebulb_Newreports_Model_Mysql4_Report_Order_Day_Updateat_Collection extends Mage_Sales_Model_Mysql4_Report_Collection_Abstract
{

    protected $_periodFormat;
    protected $_inited = false;
    protected $_selectedColumns = array(
       
        'entity_id'                 =>  '(e.entity_id)',
        'store_id'                  =>  '(e.store_id)',
        'order_date'                =>  '(e.updated_at)',
        'order_number'              =>  '(e.increment_id)',  
        //'order_cost'                =>  '(sum(item.base_cost))',  
        'total_order'               =>  'sum(e.subtotal)',  
        'discount_amount'           =>  'sum(e.discount_amount)',  
        'shipping_amt'              =>  'sum(e.shipping_amount)',  
        //'shipping_cost'             =>  'sum(si.ammount)', 
        'order_tax'                 =>  'sum(e.tax_amount)',  
        'order_invoiced'            =>  'sum(e.base_total_invoiced)',  
        'order_refunded'            =>  'sum(e.base_total_refunded)',  
        'orderstatus'               =>  'e.status',  
        'order_rep'                 =>  'e.ch_sales_source',  
        'company_name'              =>  'sfa.company',  
        'zip_code'                  =>  'sfa.postcode', 
        'state'                     =>  'sfa.region',   
        //'shipping_description'      =>  'e.shipping_description',   
        'shipping_today'            =>  'sum(e.shipping_today)' ,
        'shipping_insurance'        =>  'sum(e.shipping_insurance)', 
        'base_grand_total'          =>  'sum(e.base_grand_total)', 
        'customer_id'               =>  'e.customer_id',  
        'margin'                    =>  'sum(e.margin)', 
        'total_cost'                =>  'sum(e.total_cost)',             
        //'order_margin'              =>  ' sum( (e.subtotal - order_cost_total  ) / order_cost_total ) * 100  '
        
                                            //(($total_order - $order_cost)/$order_cost)*100
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
            $this->getSelect()->where('e.store_id IN(?) OR store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('e.store_id IN(?)', $storeIds);
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
                $this->_periodFormat = 'DATE_FORMAT(e.updated_at, \'%Y-%m\')';
            } elseif ('year' == $this->_period) {
                $this->_periodFormat = 'EXTRACT(YEAR FROM e.updated_at)';
            } else {
                $this->_periodFormat = 'DATE(e.updated_at)';
            }
            $this->_selectedColumns += array('period' => $this->_periodFormat); */
        }
        else{ 
              /*$this->_selectedColumns['total_order']  = '(SUM(e.subtotal))'; 
              $this->_selectedColumns['discount_amount']  = '(SUM(e.discount_amount))'; 
              $this->_selectedColumns['shipping_amt']  = '(SUM(e.shipping_amount))'; 
              $this->_selectedColumns['shipping_cost']  = '(SUM(si.ammount))'; 
              $this->_selectedColumns['order_tax']  = '(SUM(e.tax_amount))';*/ 
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
        
        if(Mage::app()->getRequest()->getParam('sort'))
            $sortby  = Mage::app()->getRequest()->getParam('sort');
        else
            $sortby  = 'order_date';
            
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
            $where = (!is_null($this->_from)) ? "e.updated_at >= '{$this->_from}'" : '';
            if (!is_null($this->_to)) {
                $where .= (!empty($where)) ? " AND e.updated_at <= '{$this->_to}'" : "e.updated_at <= '{$this->_to}'";
            }

            /*$subQuery = clone $this->getSelect();
            $subQuery->from(array('so' => $mainTable), array('DISTINCT DATE(so.updated_at)'))
                ->where($where); */
        }
        
        /*$subQueryrep = clone $this->getSelect();  
        $subQueryrep->from(array('sot' => 'sales_order_text'), array('order_rep'=>'sot.value')) 
                    ->joinInner(array('xea1'=>'eav_attribute'),'sot.attribute_id = xea1.attribute_id', array())
                    ->where('sot.entity_id = e.entity_id')
                    ->where("xea1.attribute_code = 'ch_sales_source'")
                    ; 
                    
        if(Mage::registry('report_filter_sales_person')){
            $salesperson = Mage::registry('report_filter_sales_person');
            if($salesperson != 'all')
                $subQueryrep->where(" sot.value =  '".$salesperson."'");     
        }*/
        
                    
        /*$subQueryfirstname = clone $this->getSelect();  
        $subQueryfirstname->from(array('xsov1' => 'sales_order_varchar'), array('customer_firstname'=>'xsov1.value')) 
                    ->joinInner(array('eav1'=>'eav_attribute'),'xsov1.attribute_id = eav1.attribute_id', array())
                    ->where('xsov1.entity_id = e.entity_id')
                    ->where("eav1.attribute_code = 'customer_firstname'")
                    ;
        
        $subQuerylastname = clone $this->getSelect();  
        $subQuerylastname->from(array('xsov1' => 'sales_order_varchar'), array('customer_lastname'=>'xsov1.value')) 
                    ->joinInner(array('eav1'=>'eav_attribute'),'xsov1.attribute_id = eav1.attribute_id', array())
                    ->where('xsov1.entity_id = e.entity_id')
                    ->where("eav1.attribute_code = 'customer_lastname'")
                    ; 
        
        $subQuerycompanyname = clone $this->getSelect();  
        $subQuerycompanyname->from(array('xsov2' => 'sales_order_entity_varchar'), array('company_name'=>'xsov2.value')) 
                    ->joinInner(array('eav1'=>'eav_attribute'),'xsov2.attribute_id = eav1.attribute_id', array())
                    ->where('xsov2.entity_id = e.entity_id')
                    ->where("eav1.attribute_code = 'company'")
                    ; 
                    
        $subQueryzipcode = clone $this->getSelect();  
        $subQueryzipcode->from(array('xsov2' => 'sales_order_entity_varchar'), array('zip_code'=>'xsov2.value')) 
                    ->joinInner(array('eav1'=>'eav_attribute'),'xsov2.attribute_id = eav1.attribute_id', array())
                    ->where('xsov2.entity_id = e.entity_id')
                    ->where("eav1.attribute_code = 'postcode'") */
                    ; 
                    
        /*$subQuerycost = clone $this->getSelect();  
        $subQuerycost->from(array('item' => 'sales_flat_order_item'), array('base_cost'=>'sum(item.base_cost)')) 
                    ->where('e.entity_id = item.order_id')
                    ->group("e.entity_id")
                    ;*/         
        
        /*$subQuerycosttotal = clone $this->getSelect();  
        $subQuerycosttotal->from(array('item' => 'sales_flat_order_item'), array('base_cost'=>'sum(item.base_cost*qty_ordered)')) 
                    ->where('e.entity_id = item.order_id')
                    ->group("e.entity_id")
                    ;*/ 
        
        /********
            Commented to hide shipping cost temporarily because of speed issues
        *******/
        /*
        $subQueryshippinhcost = clone $this->getSelect();  
        $subQueryshippinhcost->from(array('si' => 'sales_shipping_info'), array('shipping_cost'=>'sum(si.ammount)')) 
                    ->where('e.increment_id = si.order_id')
                    ->where('si.void != "Y"')
                    ->group("si.order_id")
                    ;   
         */     
        $select = $this->getSelect() 
            ->from(array('e' => $mainTable),$columns)
            //->joinLeft(array('si'=>'sales_shipping_info'),'e.increment_id = si.order_id',array())  
            ->from(array('sfa' => 'sales_flat_order_address'),array())
            //->joinLeft(array('si'=>'sales_shipping_info'),'e.increment_id = si.order_id',array())
            //->from(array('sot' => 'sales_order_text'), array('order_rep'=>'sot.value'))
            //->joinInner(array('xea1'=>'eav_attribute'),'sot.attribute_id = xea1.attribute_id', array())
                     
            ->columns("(@N := @N +1) as counter")
            //->columns("(  ".new Zend_Db_Expr($subQueryrep) . " ) as order_rep")
            ->columns("concat(e.customer_firstname,' ',e.customer_lastname) as customer_name") 
            //->columns("concat((  ". new Zend_Db_Expr($subQueryfirstname) . "),' ',( ". new Zend_Db_Expr($subQuerylastname).")) as customer_name") 
            //->columns("(  e.customer_firstname  ) as company_name") 
            //->columns("(  ".new Zend_Db_Expr($subQuerycompanyname) . " ) as company_name") 
            //->columns("(  ".new Zend_Db_Expr($subQueryzipcode) . " ) as zip_code") 
            //->columns(" sum((  ".new Zend_Db_Expr($subQuerycost) . " )) as order_cost") 
            //->columns(" sum((  ".new Zend_Db_Expr($subQuerycosttotal) . " )) as order_cost_total") 
            //->columns(" sum((  ".new Zend_Db_Expr($subQueryshippinhcost) . " )) as shipping_cost")  //Commented to hide shipping cost temporarily because of speed issues  
            /*->where('e.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                ))*/
            //->where("e.state = 'complete'")
            //->order('e.updated_at ASC')
            //->where('sot.entity_id = e.entity_id')
            //->where("xea1.attribute_code = 'ch_sales_source'")
            ->where(" sfa.parent_id = e.entity_id ")
            ->where(" sfa.address_type = 'billing' ")
            ->where($where)  
            ->order($sortby." ".$dir)
            
            ;
      
        if(Mage::registry('report_filter_order_number')){
            $select->where(" e.increment_id like '".Mage::registry('report_filter_order_number')."%' ");     
        }
        
        if(Mage::registry('report_filter_order_status')){
            $orderStatus = Mage::registry('report_filter_order_status');
            $select->where(" e.status IN (?) ",$orderStatus);     
        } 
        
        if(Mage::registry('report_filter_sales_person'))
        {
            $salesperson = Mage::registry('report_filter_sales_person');
            $select->where(" e.ch_sales_source !=  'Direct'");                             
            if($salesperson != 'all')
                $select->where(" e.ch_sales_source =  '".$salesperson."'");
            
            if(!Mage::app()->getRequest()->getParam('sort'))
                $select->order(" e.ch_sales_source asc");
             
            /*if($salesperson == 'all')  
                $select->group('sot.value');  */  
        }
        
        if(Mage::registry('report_filter_order_tax_report'))
        {
            $select->where(" e.status = 'complete'");     
            $select->where(" sfa.region = 'NY' or sfa.region = 'New York'");     
        }
        
        $select->order($sortby." ".$dir);
      
        $this->_applyStoresFilter();
         
        $this->_applyOrderStatusFilter();  
       
        /*if (!is_null($this->_from) || !is_null($this->_to)) {  
            $select->where("DATE(e.updated_at) IN(?)", new Zend_Db_Expr($subQuery));
        }*/
        
        if (!$this->isTotals()) { 
            $select->group('e.entity_id');
        } 
         
        //echo $select->__toString();exit;
        
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
