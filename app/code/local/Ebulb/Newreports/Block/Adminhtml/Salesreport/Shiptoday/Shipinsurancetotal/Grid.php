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
class Ebulb_Newreports_Block_Adminhtml_Salesreport_Shiptoday_Shipinsurancetotal_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract 
{
    protected $_columnGroupBy = 'order_date';
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('salesshippinginsurancetotal'); 
        //$this->setUseAjax(true);   
        $this->setCountTotals(true);
    }
    
           public function getResourceCollectionName()
    {   
        //return 'newreports/report_order_product_collection';
        
        return ($this->getFilterData()->getData('report_type') == 'updated_at_order')
            ? 'newreports/report_order_shippingtotal_updateat_collection'
            //: 'newreports/report_order_day_createat_collection'; 
            : 'newreports/report_order_shippingtotal_createat_collection'; 
    }
    

    protected function _prepareColumns()
    {
        $this->addColumn('counter', array(
            'header'        => Mage::helper('newreports')->__('#'),
            'index'         => 'counter',
            'width'         => 100,
            'sortable'      => false, 
            'totals_label'  => Mage::helper('newreports')->__('Total')
            
        ));
        
       
        $this->addColumn('order_date', array(
            'header'           => Mage::helper('newreports')->__('Date'),
            'index'            => 'order_date',
            'type'             => 'date',
            'totals_label'     => '',
            'width'            => 120,   
            'sortable'         => true
        ));  
       
        
        if ($this->getFilterData()->getStoreIds()) {  
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        } 
        $currency_code = $this->getCurrentCurrencyCode();
        
        $this->addColumn('base_grand_total', array(
            'header'           => Mage::helper('newreports')->__('Grand Total'),
            //'renderer'         => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Shiptoday',
            'index'            => 'base_grand_total', 
            'width'            => 100, 
            'total'            => 'sum',
            'type'             => 'currency',
            'currency_code'    => $currency_code, 
            'sortable'         => true
        ));
       
        
        $this->addColumn('shipping_insurance', array(
            'header'           => Mage::helper('newreports')->__('Ship Insurance Total'),
            //'renderer'         => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Shiptoday',
            'index'            => 'shipping_insurance', 
            'width'            => 100, 
            'total'            => 'sum',
            'type'             => 'currency',
            'currency_code'    => $currency_code, 
            'sortable'         => true
        ));
       
        
        /*$this->addColumn('action', array(
            'header'    => Mage::helper('reports')->__('Action'),
            'index'     => 'entity_id',
            'type'      => 'action',
            'align'        => 'center',
            'getter'    => 'getEntityId', 
            'width'            => 120,   
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
        ));*/   

        $this->addExportType('*/*/exportshipinsuarancetotalCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportshipinsurancetotalExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
    
}
