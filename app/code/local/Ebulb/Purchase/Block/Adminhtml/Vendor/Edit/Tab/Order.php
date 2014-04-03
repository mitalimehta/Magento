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
 * adminhtml_vendor_edit_tab_order
 */
class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Order extends Mage_Adminhtml_Block_Widget_Grid
{
    //private $_parentId;   
    
    public function __construct()
    {   
        parent::__construct();
        $this->setId('vendor_order_grid');
        $this->setDefaultSort('order_id');
        $this->setDefaultDir('DESC');  
        $this->setUseAjax(true); 
      
    }

    protected function _prepareCollection()
    { 
        $id     = $this->getRequest()->getParam('id');
        
        $collection = Mage::getModel('purchase/order')->getCollection(); 
        
        if($id != ''){
            $collection
                ->getSelect()
                ->where("`main_table`.vendor_id = ".$id); 
        }
         
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }
    
    /*public function setParentId($parentId)
    {
        $this->_parentId = $parentId;
        return $this;
    }
    
    public function getParentId()
    {  
        return $this->_parentId;
    }*/


    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header'    => Mage::helper('purchase')->__('ID'),
            'width'     => '50px',
            'index'     => 'order_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('purchase')->__('Purchase Order ID'),
            'index'     => 'increment_id', 
        ));
       
        $this->addColumn('created_date', array(
            'header'    => Mage::helper('purchase')->__('Created Date'),
            'index'     => 'created_date',
            'type'      => 'date'
        ));
        
        $this->addColumn('order_eta', array(
            'header'    => Mage::helper('purchase')->__('ETA Date'),
            'width'     => '250',
            'index'     => 'order_eta',
            'type'      => 'date'    
        )); 
        
        $this->addColumn('total', array(
            'header'=> Mage::helper('purchase')->__('Total Amount'),
            'index' => 'total',
            'renderer'  => 'Ebulb_Purchase_Block_Widget_Column_Renderer_OrderAmount',
            'align' => 'right',
            'filter'    => false,
            'sortable'  => false

        ));
        
        $this->addColumn('purchase_rep', array(
            'header'    => Mage::helper('purchase')->__('Purchase Reps.'),
            'width'     => '90',
            'index'     => 'purchase_rep',
        ));
     
        $this->addColumn('status', array(
            'header'=> Mage::helper('purchase')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('purchase/order')->getStatuses(),
            'align'    => 'right'
        ));
        
        
        
     
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('purchase')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('purchase')->__('Edit'),
                        'url'       => array('base'=> 'purchase/orders/edit'),
                        'field'     => 'po_order_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

       // $this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
       // $this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    } 
  

    public function getRowUrl($row)
    {
        return $this->getUrl('purchase/orders/edit', array('po_order_id'=>$row->getId()));
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/order', array('_current' => true));
    }
    
}
