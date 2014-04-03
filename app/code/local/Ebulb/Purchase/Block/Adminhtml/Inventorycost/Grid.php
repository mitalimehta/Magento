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
class Ebulb_Purchase_Block_Adminhtml_Inventorycost_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {

        parent::__construct();
        
        $this->setTemplate( 'purchase/inventorycost/grid.phtml');
        $this->setId('inventorycostGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC'); 
        $this->setDefaultFilter(array('sku' => 64514)); 
        $this->setSaveParametersInSession(true);
        $this->_isExport = true;
    }

    protected function _prepareCollection()
    { 
                $collection = Mage::getModel('catalog/product')->getCollection() 
                      ->addAttributeToSelect('sku')
                      ->addAttributeToSelect('name')
                      ->addAttributeToSelect('average_cost')
                      ->joinTable('cataloginventory/stock_item',
                        'product_id=entity_id',
                        array('qty'=>'qty', 
                            'total_qty' => 'total_qty', 
                            'po_qty'=>'po_qty',
                            'min_qty'=>'min_qty',
                            'max_qty'=>'max_qty'),
                        '{{table}}.stock_id=1',
                        'left')
                      ;
        
        
        
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
            'width'     => '120px',
            'index'     => 'sku'
        ));
       
        $this->addColumn('name', array(
            'header'    => Mage::helper('purchase')->__('Product Name'),
            'index'     => 'name'
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
            //'total'     =>'sum',
        ));
        
        $this->addColumn('subtotal_cost', array(
            'header'=> Mage::helper('purchase')->__('Subtotal Cost'),
            'index' => 'average_cost',
            'filter' => false,
            'sortable' => false,
            'width'     => '120px',
            'type'  => 'number',   
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_SubtotalCost',
            'align' => 'center'
        ));
        
        

        $this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    } 
   
    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_inventory/edit', array('id'=>$row->getId().$this->getTotalCost()));
    }
    
    public function getTotalCost()
    {
        $currentPageSize =    $this->getCollection()->getPageSize();
        $currentPage =    $this->getCollection()->getCurpage();
        
        $totalcost = 0;
        $this->_isExport = true;


        $this->_prepareCollection();
            
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();


        foreach ($this->getCollection() as $item) {
            if ($item->getTotalQty() > 0){
                $totalcost += $item->getTotalQty() * $item->getAverageCost();
            }
        }
        $this->_isExport = false;
        $this->_prepareGrid();        
        $this->getCollection()->setPageSize($currentPageSize);
        $this->getCollection()->setCurpage($currentPage);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
            
        return sprintf('$%.2f',$totalcost);
    }    
    
    
}
