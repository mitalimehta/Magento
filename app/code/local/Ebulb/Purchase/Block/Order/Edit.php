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
class Ebulb_Purchase_Block_Order_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	private $_purchaseOrder = null;
	
	/**
	 * Constructeur: on charge le devis
	 *
	 */
	public function __construct()
	{
        $this->_objectId = 'id';
        $this->_controller = 'order';
        $this->_blockGroup = 'purchase';
		
		parent::__construct();
        
        
        $poId = $this->getPurchaseOrder()->getId();
        
        $this->_addButton(
            'print',
            array(
                'label'     => Mage::helper('purchase')->__('Print'),
                'onclick'   => "window.location.href='".$this->getUrl('purchase/orders/print').'po_order_id/'.$poId."'",
                'level'     => -1
            )
        );

        if ($this->getPurchaseOrder()->isStatusWaitingForDelivery()
                || $this->getPurchaseOrder()->isStatusPartiallyReceived() ) {
            $this->_addButton(
                'receipt',
                array(
                    'label'     => Mage::helper('purchase')->__('Receipt'),
                    'onclick'   => "window.location.href='".$this->getUrl('purchase/receipts/new').'po_order_id/'.$poId."'",
                    'level'     => -1
                )
            );
        }
        
        if ($this->getPurchaseOrder()->isStatusNew() 
            || $this->getPurchaseOrder()->isStatusInquiry()){
            $this->_addButton(
                'cancel',
                array(
                    'label'     => Mage::helper('purchase')->__('Cancel'),
                    'onclick'   => "if (window.confirm('".Mage::helper('purchase')->__('Are you sure ?')."')) {document.location.href='".$this->getUrl('purchase/orders/cancel', array('po_order_id' => $poId))."';}",
                    'level'     => -1,
                    'class'		=> 'delete'
                )
            );
        }
	}
		
	public function getHeaderText()
    {
        return $this->__('Purchase Order #').$this->getPurchaseOrder()->getIncrementId();
    }
	
	/**
	 * 
	 */
	public function GetBackUrl()
	{
		return $this->getUrl('purchase/orders/list', array());
	}
	
	public function getPurchaseOrder()
	{
		if ($this->_purchaseOrder == null)
		{
			$this->_purchaseOrder = Mage::getModel('purchase/order')->load($this->getRequest()->getParam('po_order_id'));
		}
		return $this->_purchaseOrder;
	}
	
	public function getSaveUrl()
    {
        return $this->getUrl('purchase/orders/save');
    }
}