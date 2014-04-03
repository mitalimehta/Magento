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
class Ebulb_Newreports_Block_Adminhtml_Salesreport_Productbyrange_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract 
{
    protected $_columnGroupBy = 'product_sku';
    
    public function __construct()
    {  
        parent::__construct();
        $this->setCountTotals(true);
    }
    
    
    public function getResourceCollectionName()
    { 
       return 'newreports/report_order_productbyrange_collection';
    }
   
    protected function _prepareColumns()
    {
      
        $this->addColumn('product_sku', array(
            'header'           => Mage::helper('newreports')->__('Product'),
            'index'            => 'product_sku', 
            'totals_label'     => '',
            'sortable'         => false,
            'renderer'        => 'Ebulb_Newreports_Block_Adminhtml_Renderer_Product',   
        ));   
                    
        if ($this->getFilterData()->getStoreIds()) {    
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        } 
        $currency_code = $this->getCurrentCurrencyCode();
        
        /*$this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'product_name',
            'totals_label'     => '',
            'sortable'      => false, 
        ));*/
        
        $this->addColumn('period', array(
            'header'    =>Mage::helper('reports')->__('Period'),
            'width'     =>'170px', 
            'index'     =>'period',
            'total'     => 'sum',  
            'totals_label'     => '',    
            //'type'      =>'date',
            'sortable'  => false
        ));
                      
        $this->addColumn('sum_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity Sold'),
            'width'     =>'150px', 
            'index'     =>'sum_qty',
            'total'     => 'sum',   
            'type'      =>'number',
            'totals_label'     => '',   
            'sortable'  => false
        ));         
                      
        /*$this->addColumn('available_qty', array(
            'header'    =>Mage::helper('reports')->__('Available Stock'),
            'width'     =>'120px',
            'index'     =>'available_qty',
            'total'     => 'sum',   
            'type'      =>'number',
            'sortable'  => true,
            'totals_label'     => '',  
        ));*/
       
        $this->addExportType('*/*/exportproductbyrangeCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportproductbyrangeExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
   
}
