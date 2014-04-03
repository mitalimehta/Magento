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
class Ebulb_Purchase_Block_Adminhtml_Vendorsproduct_Grid extends Ebulb_Purchase_Block_Adminhtml_Vendorsproduct_BasefilterGrid
{
    protected $_vendorId = 0;

    public function __construct()
    {   
        parent::__construct();
        $this->setId('contactGrid');
        $this->setDefaultSort('vendor_id');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);
        $this->setTemplate('purchase/vendorsproduct/grid.phtml');
       
        $this->_vendorId = $this->getRequest()->getParam('vendor');
    }

    protected function _prepareCollection()
    { 
        $collection = Mage::getModel('catalog/product')->getCollection() 
                      ->addAttributeToSelect('sku')
                      ->addAttributeToSelect('name')
                      ->addAttributeToSelect('cost')
                      ->addAttributeToSelect('average_cost')
                      ->joinTable('cataloginventory/stock_item',
                        'product_id=entity_id',
                        array('qty'=>'qty', 
                            'total_qty' => 'total_qty', 
                            'po_qty'=>'po_qty',
                            'min_qty'=>'min_qty',
                            'max_qty'=>'max_qty'),
                        '{{table}}.stock_id=1',
                        'left');

        if ($this->_vendorId){
            $collection =  $collection->joinTable('po_vendor_product',
                        'product_id=entity_id',
                        array('vendor_product_id'=>'vendor_product_id', 
                            'vendor_sku' => 'vendor_sku'
                            ),
                        '{{table}}.vendor_id='.$this->_vendorId,
                        'inner')
                      ;   
        }
                      
        
        $collection->addAttributeToFilter('type_id', 'simple');
       
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
        
        $this->addColumn('total_qty', array(
            'header'    => Mage::helper('purchase')->__('Total Qty.'),
            'index'     => 'total_qty',
            'width'     => '120px',
            'type'      => 'number',
        ));
        
        $this->addColumn('qty', array(
            'header'    => Mage::helper('purchase')->__('Ava. Qty.'),
            'width'     => '120',
            'index'     => 'qty',
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
            'type'      => 'number', 
        ));

        $this->addColumn('min_qty', array(
            'header'    => Mage::helper('purchase')->__('Min Qty.'),
            'width'     => '120',
            'index'     => 'min_qty', 
            'type'      => 'number', 
        ));

        $this->addColumn('max_qty', array(
            'header'    => Mage::helper('purchase')->__('Max Qty.'),
            'width'     => '120',
            'index'     => 'max_qty', 
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
        return $this->getUrl('*/adminhtml_inventory/edit', array('id'=>$row->getId()));
    }
    
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }
    

    
}
