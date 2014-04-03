<?php

class Ebulb_Purchase_Block_Order_Edit_Tabs_Receipts extends Mage_Adminhtml_Block_Widget_Grid
{
    private $_purchaseOrder = null;
    private $_orderId = null;
    
    public function setOrderId($value)
    {
        $this->_orderId = $value;
        $this->_purchaseOrder = Mage::getModel('purchase/order')->load($value);
        return $this;
    }
    
    public function getOrder()
    {
        return $this->_purchaseOrder;
    }
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('Receipts');
        $this->setUseAjax(true);
        $this->setEmptyText('No records');
    }

    /**
     * 
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {                    
        $collection = Mage::getModel('purchase/receipt')
            ->getCollection()
            ->addFieldToFilter('purchase_order_id', $this->_orderId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
   /**
     * 
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
            
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('purchase')->__('Receipt Id'),
            'sortable'  => true,
            'width'     => '160px',
            'index'     => 'increment_id'
        ));
        
        $this->addColumn('package_number', array(
            'header'=> Mage::helper('purchase')->__('Package Number'),
            'index' => 'package_number',
            'width'     => '160px',
        ));
                   
        $this->addColumn('created_date', array(
            'header'=> Mage::helper('purchase')->__('Created Date'),
            'index' => 'created_date',
            'type' => 'date',
        ));
        /*
        $this->addColumn('cancel_date', array(
            'header'=> Mage::helper('purchase')->__('Cancel Date'),
            'index' => 'cancel_date',
            'filter'    => false,
        ));
        */

        
        return parent::_prepareColumns();
    }
    
    
     public function getGridUrl()
    {
        return $this->getUrl('*/*/receiptsGrid', array('_current' => true, 'po_order_id' => $this->getRequest()->getParam('po_order_id'))); 
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('purchase/receipts/edit', array())."po_receipt_id/".$row->getId().'/po_order_id/'.$this->_orderId.'/';
    }
}
