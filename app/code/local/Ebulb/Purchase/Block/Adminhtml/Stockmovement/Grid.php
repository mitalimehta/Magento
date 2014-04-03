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
class Ebulb_Purchase_Block_Adminhtml_Stockmovement_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct()
    { 
        parent::__construct();
        $this->setId('stock_movement_grid');     
        $this->setDefaultSort('created_date');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);  
    }

    protected function _prepareCollection()
    { 
        $id     = $this->getRequest()->getParam('id');
        
        $collection = Mage::getModel('purchase/stockmovement')->getCollection();  
        
        $collection
                ->getSelect()
                ->joinLeft(array('product'=>'catalog_product_entity'),'product.entity_id = `main_table`.product_id',array('sku'=>'product.sku'))  
                ->joinLeft(array('productname'=>'catalog_product_entity_varchar'),'productname.entity_id = `product`.entity_id',array('value'=>'productname.value'))
                ->where('productname.attribute_id = 60');
        
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('stock_movement_id', array(
            'header'    => Mage::helper('purchase')->__('ID'),
            'width'     => '50px',
            'index'     => 'stock_movement_id',
            'type'  => 'number',
        ));
       
        $this->addColumn('created_date', array(
            'header'    => Mage::helper('purchase')->__('Created At'),
            'index'     => 'created_date',
            'type'     => 'date',
        ));
        
        $this->addColumn('sku', array(
            'header'    => Mage::helper('purchase')->__('SKU'),
            'width'     => '150',
            'index'     => 'sku'
        ));
        
        $this->addColumn('value', array(
            'header'    => Mage::helper('purchase')->__('Product Name'),
            'index'     => 'value'
        ));
        
        $this->addColumn('product_qty', array(
            'header'    => Mage::helper('purchase')->__('Quantity'),
            'width'     => '120',
            'index'     => 'product_qty', 
            'renderer'  => 'Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Renderer_Quantity',
        ));
        
        $this->addColumn('doc_id', array(
            'header'    => Mage::helper('purchase')->__('Sales Order / Purchase Order ID'),
            'width'     => '150',
            'index'     => 'doc_id'
        ));
        
        $this->addColumn('comments', array(
            'header'    => Mage::helper('purchase')->__('Comments'),
            'index'     => 'comments',   
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    } 

    public function getRowUrl($row)
    {
       /// return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    } 
    
}
