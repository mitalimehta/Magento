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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Newreports_Block_Adminhtml_Salesreport_Day_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract 
{
    protected $_columnGroupBy = 'period';
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_id'); 
        //$this->setUseAjax(true);   
        $this->setCountTotals(true);
    }
    
    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        
        if ($filterData->getData('from') == null || $filterData->getData('to') == null) {    
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return parent::_prepareCollection();
        }
                      
        $resourceCollection = Mage::getResourceModel($this->getResourceCollectionName())
            ->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter(explode(',', $filterData->getData('store_ids')))
            ->addOrderStatusFilter($filterData->getData('order_statuses'))
            ->setAggregatedColumns($this->_getAggregatedColumns());
        
        //echo "<pre>";print_r(get_class_methods($resourceCollection->getData()));
        
        if ($this->_isExport) {
            $this->setCollection($resourceCollection);
            return $this;
        }

        /*if ($filterData->getData('show_empty_rows', false)) {
            Mage::helper('reports')->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData('from', null),
                $filterData->getData('to', null),
                $filterData->getData('period_type')
            );
        }*/

        if ($this->getCountSubTotals()) {
            $this->getSubTotals();
        }
         
        if ($this->getCountTotals()) {
            $totalsCollection = Mage::getResourceModel($this->getResourceCollectionName())
                ->setPeriod($filterData->getData('period_type'))
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
                ->addStoreFilter(explode(',', $filterData->getData('store_ids')))
                ->addOrderStatusFilter($filterData->getData('order_statuses'))
                ->setAggregatedColumns($this->_getAggregatedColumns())
                ->isTotals(true); 
                
            //foreach ($totalsCollection as $item) { 
            //    $this->setTotals($item);
            //    break;
            //}
        }

        $this->getCollection()->setColumnGroupBy($this->_columnGroupBy);
        $this->getCollection()->setResourceCollection($resourceCollection);              
     
        /*echo "<pre>";
        print_r(get_class_methods($resourceCollection));
        print_r($resourceCollection->getData());
        exit;*/
        
        
       //echo "<pre>";print_r(get_class_methods($resourceCollection->getData())); exit;
        
        Mage::register("report_collection",$resourceCollection,true); 
        return parent::_prepareCollection();
    }

    public function getResourceCollectionName()
    {   
        //return 'newreports/report_order_product_collection';
        
        return ($this->getFilterData()->getData('report_type') == 'updated_at_order')
            ? 'newreports/report_order_day_updateat_collection'
            : 'newreports/report_order_day_createat_collection'; 
    }
    

    protected function _prepareColumns()
    {
        $this->addColumn('counter', array(
            'header'        => Mage::helper('newreports')->__('#'),
            'index'         => 'counter',
            'width'         => 50,
            'sortable'      => false, 
            'totals_label'  => Mage::helper('newreports')->__('Total')
            
        ));
        
        $this->addColumn('order_number', array(
            'header'           => Mage::helper('newreports')->__('Order #'),
            'index'            => 'order_number',
            'totals_label'     => '',
            'sortable'         => true
        )); 
        
        $this->addColumn('order_date', array(
            'header'           => Mage::helper('newreports')->__('Date'),
            'index'            => 'order_date',
            'type'             => 'date',
            'totals_label'     => '',
            'width'            => 120,   
            'sortable'         => true
        ));  
        
        $this->addColumn('order_rep', array(
            'header'           => Mage::helper('newreports')->__('Sales Rep'),
            'renderer'         => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Rep',
            'index'            => 'order_rep', 
            'width'            => 100, 
            'totals_label'     => '',
            'totals_label'     => '',
            'sortable'         => true
        ));
        
        if ($this->getFilterData()->getStoreIds()) {  
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        } 
        $currency_code = $this->getCurrentCurrencyCode();
        
        $this->addColumn('customer_name', array(
            'header'           => Mage::helper('newreports')->__('Name'),
            'index'            => 'customer_name', 
            'width'            => 100, 
            'totals_label'     => '',
            'sortable'         => true
        ));
        
        $this->addColumn('company_name', array(
            'header'           => Mage::helper('newreports')->__('Company'),
            'index'            => 'company_name', 
            'width'            => 100, 
            'totals_label'     => '',
            'sortable'         => true
        ));
        
        $this->addColumn('zip_code', array(
            'header'           => Mage::helper('newreports')->__('Zip Code'),
            'index'            => 'zip_code', 
            'width'            => 100, 
            'totals_label'     => '',
            'sortable'         => true
        ));
        
        /*$this->addColumn('order_cost', array(
            'header'           => Mage::helper('newreports')->__('Cost'),
            'index'            => 'order_cost', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        )); */
        
        $this->addColumn('total_cost', array(
            'header'           => Mage::helper('newreports')->__('Total Cost'),
            'index'            => 'total_cost', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        ));
        
        
        
        $this->addColumn('total_order', array(
            'header'           => Mage::helper('newreports')->__('Subtotal'),//('Total Order'),
            'index'            => 'total_order', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        )); 
        
        $this->addColumn('discount_amount', array(
            'header'           => Mage::helper('newreports')->__('Discounts'),
            'index'            => 'discount_amount', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        ));
        
         $this->addColumn('order_invoiced', array(
            'header'           => Mage::helper('newreports')->__('Total Invoiced'),
            'index'            => 'order_invoiced', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        ));
        
         $this->addColumn('order_refunded', array(
            'header'           => Mage::helper('newreports')->__('Total Refunded'),
            'index'            => 'order_refunded', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        ));
        
        
        /********
            Commented to hide shipping cost temporarily because of speed issues
        *******/
        
        /*
         $this->addColumn('shipping_cost', array(
            'header'           => Mage::helper('newreports')->__('Shipping Cost'),
            'index'            => 'shipping_cost', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        ));
        */
        
        $this->addColumn('shipping_amt', array(
            'header'           => Mage::helper('newreports')->__('Shipping'),
            'index'            => 'shipping_amt', 
            'width'            => 100, 
            'total'            => 'sum', 
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true
        )); 
       
        
        $this->addColumn('shipping_today', array(
            'header'           => Mage::helper('newreports')->__('Ship Today'),
            //'renderer'         => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Shiptoday',
            'index'            => 'shipping_today', 
            'width'            => 100, 
            'total'            => 'sum',
            'type'             => 'currency',
            'currency_code'    => $currency_code, 
            'sortable'         => true
        ));
        
        $this->addColumn('shipping_insurance', array(
            'header'           =>Mage::helper('reports')->__('Ship Insurance'),
            //'renderer'         => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Shipinsurance', 
            'index'            =>'shipping_insurance',
            'total'            => 'sum', 
             'width'            => 100,  
            'type'             => 'currency',
            'currency_code'    => $currency_code,
            'sortable'         => true, 
        )); 
        
        /*$this->addColumn('order_upgrade', array(
            'header'        =>Mage::helper('reports')->__('Upgrade'),
            'index'         =>'order_upgrade',
            'total'         => 'sum', 
            'width'            => 100,   
            'sortable'      => false, 
        ));*/
        
        $this->addColumn('order_tax', array(
            'header'            =>Mage::helper('reports')->__('Tax'),
            'index'             =>'order_tax',
            'total'             => 'sum', 
            'type'              => 'currency',
            'currency_code'     => $currency_code,
            'sortable'         => true, 
        ));        
                      
        $this->addColumn('margin', array(
            'header'        => Mage::helper('newreports')->__('Margin (%)'),
            //'renderer'      => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Margin', 
            //'type'          => 'currency',
            //'currency_code' => $currency_code, 
            'type'             => 'number',  
            'index'         => 'margin', 
            'sortable'      => true,
            //'totals_label'     => '',   
            //'totals_label'  => Mage::getSingleton('core/session')->getSalesReportTotalMargin(),
        ));  
        
        $this->addColumn('orderstatus', array(
            'header'        =>Mage::helper('reports')->__('Status'),
            'index'         =>'orderstatus',
            'width'            => 100,   
            'totals_label'  => '',  
            'sortable'      => true, 
        ));
        
        $this->addColumn('action', array(
            'header'    => Mage::helper('reports')->__('Action'),
            'index'     => 'entity_id',
            'type'      => 'action',
            'align'        => 'center',
            'getter'    => 'getEntityId', 
            'totals_label'  => '',  
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption' =>  Mage::helper('reports')->__('View'),
                    'url'     => $this->getUrl('/index.php').'admin/sales_order/view/order_id/$entity_id', //.$row->getEntityId(), //array('base'=>'sales_order/view/order_id/$entity_id'),
                    'field'   => 'entity_id',
                    'target'  => '_blank'
                )
            ),
            'is_system' => true, 
        ));   

        $this->addExportType('*/*/exportsaleperdayCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportsaleperdayExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
    
   
 
}
