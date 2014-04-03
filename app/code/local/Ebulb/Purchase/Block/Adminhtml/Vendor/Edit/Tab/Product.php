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
class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid   
{
    static  $_guidSequence = 1;
    private $_guid = null;
    private $_parentId;   
    static  $_rowCount = 0;  
    
    public function __construct()
    {  
        parent::__construct();
        $this->setId('ProductGrid');
        $this->setDefaultSort('vendor_product_id');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);
        $this->_parentTemplate = $this->getTemplate();
        //$this->setTemplate('catalogupgrade/list.phtml');          
        //$this->setEmptyText('No records');
        $this->setEmptyText('No records');
        //$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->_defaultLimit = 0;
    }
    
    public function setParentId($parentId)
    {
        $this->_parentId = $parentId;
        return $this;
    }
    
    public function getParentId()
    {  
        return $this->_parentId;
    }

    protected function _prepareCollection()
    { 
        $id     = $this->getRequest()->getParam('id');
       
        $collection = Mage::getModel('purchase/product')->getCollection();  
        
        if($id != ''){
            $collection
                ->getSelect()
                ->where("`main_table`.vendor_id = ".$id);
            Mage::register('product_vendor_id',$id); 
        }
            
        else if($this->getParentId()){
            $collection
                ->getSelect()
                ->where("`main_table`.vendor_id = ".$this->getParentId());    
            Mage::register('product_vendor_id',$this->getParentId()); 
        } 
        
        $collection
                ->getSelect()
                ->joinLeft(array('product'=>'catalog_product_entity'),'product.entity_id = `main_table`.product_id',array('product_sku'=>'product.sku'))  
                ->joinLeft(array('productname'=>'catalog_product_entity_varchar'),'productname.entity_id = `product`.entity_id',array('product_name'=>'productname.value'))
                ->where('productname.attribute_id = 60')
                ->where('productname.store_id = 0');
       
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('vendor_product_id', array(
            'header'        => Mage::helper('purchase')->__('ID'),
            'width'         => '50px',
            'index'         => 'vendor_product_id',
            'type'          => 'number',
            'sortable'      => false,    
        ));
        
        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('purchase')->__('SKU#'),
            'index'     => 'product_sku',
            'sortable'      => false, 
            'width'         => '150px',     
        ));
        
        $this->addColumn('product_name', array(
            'header'    => Mage::helper('purchase')->__('Product Name'),
            'index'     => 'product_name',
            'sortable'      => false,    
        ));
        
        $this->addColumn('vendor_sku', array(
            'header'    => Mage::helper('purchase')->__('Vendor Ref#'),
            'width'     => '250',
            'index'     => 'vendor_sku',
            'sortable'      => false,    
        )); 

        $this->addColumn('unit_cost', array(
            'header'    => Mage::helper('purchase')->__('Cost'),
            'width'     => '150',
            'index'     => 'unit_cost',
            'sortable'      => false,    
        ));
        
        $this->addColumn('action', array(
            'header'=> Mage::helper('Catalog')->__('Action'),
            'index' => 'action',
            'renderer'=> 'Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Renderer_Productaction',
            'filter'    => false,
            'sortable'  => false,
            'align'     => 'center',
            'guid' => $this->getGuid(),
            //'ParentId' => $this->getParentId()
        ));
       
        //$this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    }  
    
    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    
    public function setGuid($guid)
    {
            $this->_guid = $guid;
            return $this;
    }
    
    public function getGuid()
    {
        if($this->_guid == null)
           if($this->getRequest()->getParam('guid') != '')
              $this->_guid = $this->getRequest()->getParam('guid');
           else
           {
              $this->_guid = Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Product::$_guidSequence;
              Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Product::$_guidSequence += 1;
          }
        return $this->_guid;
    }
    
}
