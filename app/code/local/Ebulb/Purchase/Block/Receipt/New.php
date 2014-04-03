<?php

class Ebulb_Purchase_Block_Receipt_New extends Mage_Adminhtml_Block_Widget_Form
{
    private $_order = null;   		
    
	public function __construct()
	{
		parent::__construct();
	}
	
	public function GetBackUrl()
	{
		return $this->getUrl('purchase/orders/edit', array('po_order_id' => Mage::app()->getRequest()->getParam('po_order_id', false)));
	}

    /**
     * 
     *
     * @return unknown
     */
    public function getOrder()
    {
        if ($this->_order == null)
        {
            $po_order_id = Mage::app()->getRequest()->getParam('po_order_id', false);    
            $model = Mage::getModel('purchase/order');
            $this->_order = $model->load($po_order_id);
        }
        return $this->_order;
    }
    
    public function getProducts()
    {
        return $this->getOrder()->getOrderItems();
    }    

}
