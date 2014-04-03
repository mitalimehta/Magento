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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Purchase_Block_Adminhtml_Inventory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {   
        parent::__construct();
        $this->setId('contactGrid');
        $this->setDefaultSort('vendor_id');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);
       
    }

    protected function _prepareCollection()
    { 
        $collection = Mage::getModel('catalog/product')->getCollection() 
                      ->addAttributeToSelect('entity_id')
                      ->addAttributeToSelect('sku')
                      ->addAttributeToSelect('name')
                      ->addAttributeToSelect('average_cost')
                      ->addAttributeToSelect('warehouse_bin_number')
                      ->addAttributeToSelect('cost')
                      ->joinField('qty',
                        'cataloginventory/stock_item',
                        'qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left')
                      ->joinField('total_qty',
                        'cataloginventory/stock_item',
                        'total_qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left')
                     ->joinField('po_qty',
                        'cataloginventory/stock_item',
                        'po_qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left') 
                     ->joinField('max_qty',
                        'cataloginventory/stock_item',
                        'max_qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left')      
                      ;
        
        
        
        $collection->addAttributeToFilter('type_id', 'simple')
        ->addAttributeToFilter('status', 1);
       
        /*echo $collection
            ->getSelect()->__toString();exit; */
         
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }   

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('purchase')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('sku', array(
            'header'    => Mage::helper('purchase')->__('SKU'),
            'index'     => 'sku'
        ));
       
        $this->addColumn('name', array(
            'header'    => Mage::helper('purchase')->__('Product Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('cost', array(
            'header'    => Mage::helper('purchase')->__('Cost'),
            'index'     => 'cost',
            'width'     => '120px',
            'type'      => 'number',
            //'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));
        
        $this->addColumn('average_cost', array(
            'header'    => Mage::helper('purchase')->__('Average Cost'),
            'index'     => 'average_cost',
            'width'     => '120px',
            'type'      => 'number',
            //'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));
        
        $this->addColumn('warehouse_bin_number', array(
            'header'    => Mage::helper('purchase')->__('Bin No'),
            'index'     => 'warehouse_bin_number',
            'width'     => '120px',
        ));
        
        $this->addColumn('total_qty', array(
            'header'    => Mage::helper('purchase')->__('Total Qty.'),
            'index'     => 'total_qty',
            'width'     => '120px',
            'renderer'  => 'Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Totalqty',
            'type'      => 'number',
        ));
        
        $this->addColumn('qty', array(
            'header'    => Mage::helper('purchase')->__('Ava. Qty.'),
            'width'     => '120',
            'index'     => 'qty',
            'renderer'  => 'Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Qty',
            'type'      => 'number',
        )); 

        $this->addColumn('so_qty', array(
            'header'    => Mage::helper('purchase')->__('SO Qty.'),
            'width'     => '120',
            'index'     => 'so_qty',
            'renderer'  => 'Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Salesorderqty',
            'type'      => 'number', 
        ));
        
        $this->addColumn('po_qty', array(
            'header'    => Mage::helper('purchase')->__('PO Qty.'),
            'width'     => '120',
            'index'     => 'po_qty',
            'renderer'  => 'Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Poqty',
            'type'      => 'number', 
        ));
        
        $this->addColumn('stock_needed', array(
            'header'    => Mage::helper('purchase')->__('Stock Needed'),
            'width'     => '120',
            'index'     => 'stock_needed', 
            'renderer'  => 'Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Stockneeded',
            'type'      => 'number', 
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    } 
   
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
    
}
