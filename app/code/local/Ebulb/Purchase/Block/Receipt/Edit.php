<?php

class Ebulb_Purchase_Block_Receipt_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	private $_order = null;
    private $_receipt = null;
    private $_invoice = null;
	
	/**
	 * 
	 *
	 */
	public function __construct()
	{
        $this->_objectId = 'id';
        $this->_controller = 'receipts';
        $this->_blockGroup = 'purchase';
		
		parent::__construct();
        
        
        $poId = $this->getOrder()->getId();
        
		$this->_addButton(
            'print',
            array(
                'label'     => Mage::helper('purchase')->__('Print'),
                'onclick'   => "window.location.href='".$this->getUrl('purchase/orders/print').'po_order_id/'.$poId."'",
                'level'     => -1
            )
        );


	}
		
	public function getHeaderText()
    {
        return $this->__('Receipt for Purchase Order #').$this->getOrder()->getIncrementId();
    }
	
	/**
	 * 
	 */
	public function GetBackUrl()
	{
		return $this->getUrl('purchase/orders/edit', array('po_order_id' => $this->getOrder()->getId()));
	}
	

	
	public function getSaveUrl()
    {
        return $this->getUrl('purchase/receipts/save');
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
            $po_order_id = $this->getReceipt()->getPurchaseOrderId();    
            $model = Mage::getModel('purchase/order');
            $this->_order = $model->load($po_order_id);
        }
        return $this->_order;
    }
    
    public function getProducts()
    {
        return $this->getOrder()->getOrderItems();
    }    

     /**
     * 
     *
     * @return unknown
     */
    public function getReceipt()
    {
        if ($this->_receipt == null)
        {
            $po_receipt_id = Mage::app()->getRequest()->getParam('po_receipt_id', false);    
            $model = Mage::getModel('purchase/receipt');
            $this->_receipt = $model->load($po_receipt_id);
        }
        return $this->_receipt;
    }
    
     /**
     * 
     *
     * @return unknown
     */
    public function getInvoice()
    {
        if ($this->_invoice == null)
        {
            $po_receipt_id = Mage::app()->getRequest()->getParam('po_receipt_id', false);    
            $this->_invoice = Mage::getModel('purchase/invoice')->loadByReceiptId($po_receipt_id);
            
        }
        return $this->_invoice;
    }       
}