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
class Ebulb_Newreports_Block_Adminhtml_Salesreport_Product_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract 
{
    protected $_columnGroupBy = 'period';
    
    public function __construct()
    {
        parent::__construct();
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

        if ($this->_isExport) {
            $this->setCollection($resourceCollection);
            return $this;
        }

        if ($filterData->getData('show_empty_rows', false)) {
            Mage::helper('reports')->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData('from', null),
                $filterData->getData('to', null),
                $filterData->getData('period_type')
            );
        }

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
            foreach ($totalsCollection as $item) {
                $this->setTotals($item);
                break;
            }
        }

        $this->getCollection()->setColumnGroupBy($this->_columnGroupBy);
        $this->getCollection()->setResourceCollection($resourceCollection);

        Mage::register("report_collection",$resourceCollection,true); 
        
        return parent::_prepareCollection();
    }

    public function getResourceCollectionName()
    {   
       
        return ($this->getFilterData()->getData('report_type') == 'updated_at_order')
            ? 'newreports/report_order_product_updateat_collection'
            : 'newreports/report_order_product_createat_collection'; 
    }
   
    protected function _prepareColumns()
    {
        $this->addColumn('counter', array(
            'header'        => Mage::helper('newreports')->__('Number'),
            'index'         => 'counter',
            'width'         => 100,
            'sortable'      => false, 
            'totals_label'  => Mage::helper('newreports')->__('Total')
            
        ));   
        
        $this->addColumn('product_sku', array(
            'header'           => Mage::helper('newreports')->__('SKU'),
            'index'            => 'product_sku', 
            'width'            => 100, 
            'totals_label'     => '',
            'sortable'         => true
        ));   
                    
        if ($this->getFilterData()->getStoreIds()) {    
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        } 
        $currency_code = $this->getCurrentCurrencyCode();
        
        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'product_name',
            'totals_label'     => '',
            'sortable'      => true, 
        ));
                      
        $this->addColumn('sum_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity Sold'),
            'width'     =>'120px',
            //'align'     =>'center',
            'index'     =>'sum_qty',
            'total'     => 'sum',   
            'type'      =>'number',
            'sortable'  => true
        ));         
                      
        $this->addColumn('product_cost', array(
            'header'        => Mage::helper('newreports')->__('Average Cost'),
            'type'          => 'currency',
            'renderer'      => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Costtotal', 
            'currency_code' => $currency_code,
            'index'         => 'product_cost',   
            'total'         => 'sum', 
            'sortable'      => true
        ));
        
        
       
        
        $this->addColumn('product_sellprice', array(
            'header'        => Mage::helper('newreports')->__('Sell Price'),
            'type'          => 'currency',
            'renderer'      => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Sellpricetotal', 
            'currency_code' => $currency_code,
            'index'         => 'product_sellprice',  
            'total'         => 'sum', 
            'sortable'      => true
        ));
        
         $this->addColumn('product_sellprice_subtotal', array(
            'header'        => Mage::helper('newreports')->__('Subtotal'),
            'type'          => 'currency',
            'renderer'      => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Subtotaltotal',
            'currency_code' => $currency_code,
            'index'         => 'product_sellprice_subtotal',
            'total'         => 'sum', 
            'sortable'      => true
        ));

        $this->addColumn('available_qty', array(
            'header'    =>Mage::helper('reports')->__('Available Stock'),
            'width'     =>'120px',
            'index'     =>'available_qty',
            'total'     => 'sum',   
            'type'      =>'number',
            'sortable'  => true,
            'totals_label'     => '',  
        ));         

        $this->addExportType('*/*/exportproductCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportproductExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
   
}
