<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ebulb_Purchase_Block_Adminhtml_Tools_Massstockeditor extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {  
        parent::__construct();
        $this->setId('MassStockEditorGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('purchase')->__('No Items'));
    }

    /**
     * Charge la collection
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {            
        //Recupere les paramétrages par défaut
        $DefaultNotifyStockQty = Mage::getStoreConfig('cataloginventory/item_options/notify_stock_qty');
        if ($DefaultNotifyStockQty == '')
            $DefaultNotifyStockQty = 0;
            
        //charge
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('manufacturer')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('master_sku')
            ->addAttributeToSelect('ordered_qty')
            ->addAttributeToSelect('warehouse_bin_number')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('cost')
            ->addAttributeToSelect('average_cost')
            ->joinField('stock_qty',
                'cataloginventory/stock_item',
                'total_qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->joinField('notify_stock_qty',
                'cataloginventory/stock_item',
                'notify_stock_qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            ->joinField('use_config_notify_stock_qty',
                'cataloginventory/stock_item',
                'use_config_notify_stock_qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left')
            /*->addExpressionAttributeToSelect('real_notify_stock_qty',
                'if(`_table_stock_qty`.`use_config_notify_stock_qty` = 0, `_table_stock_qty`.`notify_stock_qty`, '.$DefaultNotifyStockQty.')',
                 array())                                                                                                                     */
            ->addExpressionAttributeToSelect('real_notify_stock_qty',
                'if(`_table_stock_qty`.`use_config_notify_stock_qty` = 0, `_table_stock_qty`.`min_qty`, '.$DefaultNotifyStockQty.')',
                 array())
            ->addExpressionAttributeToSelect('max_stock_qty',
                'if(`_table_stock_qty`.`use_config_notify_stock_qty` = 0, `_table_stock_qty`.`max_qty`, '.$DefaultNotifyStockQty.')',
                 array()) 
            ;
            
        $collection->addAttributeToFilter('type_id', 'simple');
        //echo $collection->getSelect()->__toString();exit;
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
   /**
     * Défini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
                               
        $this->addColumn('sku', array(
            'header'=> Mage::helper('sales')->__('Sku'),
            'index' => 'sku'
        ));
        
        /*$this->addColumn('master_sku', array(
            'header'=> Mage::helper('sales')->__('Master Sku'),
            'index' => 'master_sku'
        ));*/               
        
        $this->addColumn('name', array(
            'header'=> Mage::helper('sales')->__('Name'),
            'index' => 'name'
        ));
    
        /*$this->addColumn('manufacturer', array(
            'header'=> Mage::helper('sales')->__('Manufacturer'),
            'index' => 'manufacturer',
            'type' => 'options',
            'options' => $this->getManufacturers()
        ));*/
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => '80',
            'index'     => 'status',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        
        $this->addColumn('entity_id', array(
            'header'=> Mage::helper('purchase')->__(''),
            'index' => 'entity_id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_Massstockeditor_Editcheck',
            'align' => 'center'
        ));
        
        $this->addColumn('warehouse_bin_number', array(
            'header'=> Mage::helper('purchase')->__('Bin No'),
            'index' => 'warehouse_bin_number',
            'filter' => false,
            'sortable' => false,
            'width'     => '140',    
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_Massstockeditor_BinNumber',
            'align' => 'center'
        ));
        
        $this->addColumn('cost', array(
            'header'=> Mage::helper('purchase')->__('Cost'),
            'index' => 'cost',
            'filter' => false,
            //'type'  => 'number',   
            'sortable' => false,
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_Massstockeditor_Cost',
            'align' => 'center'
        ));
        
        $this->addColumn('average_cost', array(
            'header'=> Mage::helper('purchase')->__('Average Cost'),
            'index' => 'average_cost',
            'filter' => false,
            'sortable' => false,
            //'type'  => 'number',   
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_Massstockeditor_AverageCost',
            'align' => 'center'
        ));
        
                
        $this->addColumn('stock_mini', array(
            'header'=> Mage::helper('purchase')->__('Minimum Stock'),
            'index' => 'stock_mini',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_Massstockeditor_StockMini',
            'align' => 'center'
        ));
        
        $this->addColumn('stock_max', array(
            'header'=> Mage::helper('purchase')->__('Maximum Stock'),
            'index' => 'stock_mini',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_Massstockeditor_StockMax',
            'align' => 'center'
        ));
        

        $this->addColumn('stock', array(
            'header'=> Mage::helper('purchase')->__('Total Stock'),
            'index' => 'stock',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Ebulb_Purchase_Block_Adminhtml_Widget_Column_Renderer_Massstockeditor_Stock',
            'align' => 'center'
        ));
        
        return parent::_prepareColumns();
    }

     public function getGridUrl()
    {
        return ''; //$this->getUrl('*/*/wishlist', array('_current'=>true));
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    

    /**
     * Définir l'url pour chaque ligne
     * permet d'accéder à l'écran "d'édition" d'une commande
     */
    public function getRowUrl($row)
    {
        return '';
    }
    
    private function getManufacturers()
    {
      $product = Mage::getModel('catalog/product');
      $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                  ->setEntityTypeFilter($product->getResource()->getTypeId())
                  ->addFieldToFilter('attribute_code', 'manufacturer');
      $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
      $manufacturers = $attribute->getSource()->getAllOptions(false);
      
      $retour = array();
      foreach ($manufacturers as $manufacturer)
      {
         $retour[$manufacturer['value']] = $manufacturer['label'];
      }
      
      return $retour;
      
      
    }

}
