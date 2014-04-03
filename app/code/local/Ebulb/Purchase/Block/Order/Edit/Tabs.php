<?php

class Ebulb_Purchase_Block_Order_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	private $_purchaseOrder = null;
	
    public function __construct()
    {
        parent::__construct();
        $this->setId('purchase_order_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('');
    }

    protected function _beforeToHtml()
    {
        $product = $this->getProduct();
        $orderStatus = $this->getPurchaseOrder()->getStatus();
        
        $this->addTab('tab_info', array(
            'label'     => Mage::helper('purchase')->__('Summary'),
            'content'   => $this->getLayout()->createBlock('purchase/order_edit_tabs_info')->toHtml(),
        ));
        
        if($this->getPurchaseOrder()->isStatusNew()
           || $this->getPurchaseOrder()->isStatusInquiry()
           || $this->getPurchaseOrder()->isStatusWaitingForDelivery()
           || $this->getPurchaseOrder()->isStatusPartiallyReceived())
        {
            $this->addTab('tab_add_products', array(
                'label'     => Mage::helper('purchase')->__('Add Products'),
                'url'   => $this->getUrl('*/*/productSelectionGrid', array('_current'=>true, 'po_order_id' => $this->getRequest()->getParam('po_order_id'))),
                'class' => 'ajax',
            ));        
        }

        $this->addTab('tab_products', array(
            'label'     => Mage::helper('purchase')->__('Review Products'),
            'content'   => $this->getLayout()->createBlock('purchase/order_edit_tabs_products')->toHtml(),
        ));
        
        if ($this->getPurchaseOrder()->isStatusInquiry())
        {
            $this->addTab('tab_send_to_supplier', array(
                'label'     => Mage::helper('purchase')->__('Send order to vendor'),
                'content'   => $this->getLayout()->createBlock('purchase/order_edit_tabs_sendToVendor')->setOrderId($this->getPurchaseOrder()->getId())->toHtml(),
            ));
        }

        if ($this->getPurchaseOrder()->isStatusCanceled() 
            || $this->getPurchaseOrder()->isStatusComplete() 
            || $this->getPurchaseOrder()->isStatusWaitingForDelivery()
            || $this->getPurchaseOrder()->isStatusPartiallyReceived())
        {
            $this->addTab('tab_receipts', array(
                'label'     => Mage::helper('purchase')->__('Receipts'),
                'class'     => 'ajax',
                'url'   => $this->getUrl('*/*/receiptsGrid', array('_current'=>true, 'po_order_id' => $this->getRequest()->getParam('po_order_id'))),
            ));
        }
        //set active tab
        $defaultTab = $this->getRequest()->getParam('tab');
        if ($defaultTab == null)
        	$defaultTab = 'tab_info';
        $this->setActiveTab($defaultTab);
        return parent::_beforeToHtml();
    }

    /**
     * Retrive product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getPurchaseOrder()
    {
		if ($this->_purchaseOrder == null)
		{
			$this->_purchaseOrder = Mage::getModel('purchase/order')->load($this->getRequest()->getParam('po_order_id'));
		}
		return $this->_purchaseOrder;
    }

}
