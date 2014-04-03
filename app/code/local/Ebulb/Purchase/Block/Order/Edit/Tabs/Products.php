<?php

class Ebulb_Purchase_Block_Order_Edit_Tabs_Products extends Mage_Adminhtml_Block_Widget_Form
{
	private $_order = null;
	
	/**
	 * Constructeur: on charge
	 *
	 */
	public function __construct()
	{
		
		parent::__construct();
		
		$this->setTemplate('purchase/order/edit/tab/products.phtml');
	}	
	
		
	/**
	 * Retourne l'objet
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
	
	/**
	 * Retourne la liste des produits de la commande
	 *
	 */
	public function getProducts()
	{		
		return $this->getOrder()->getOrderItems();
	}
	
	/**
	 * Retourne le dernier prix d'achat sans frais d'approche pour un produit
	 *
	 * @param unknown_type $ProductId
	 */
	public function GetLastPriceWithoutFees($ProductId)
	
    {
        /*
		$sql = 'select pop_price_ht_base from '.mage::getModel('Purchase/Constant')->getTablePrefix().'purchase_order_product, '.mage::getModel('Purchase/Constant')->getTablePrefix().'purchase_order where pop_order_num = po_num and po_status = \''.MDN_Purchase_Model_Order::STATUS_COMPLETE.'\' and pop_price_ht_base > 0 and pop_product_id = '.$ProductId.' order by po_num DESC LIMIT 1';
		$retour = mage::getResourceModel('sales/order_item_collection')->getConnection()->fetchOne($sql);
		$retour = number_format($retour, 2);
		return $retour;
        */
        return 'disabled';
	}
	
}
	