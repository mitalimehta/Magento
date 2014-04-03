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
class Ebulb_Purchase_Block_Order_Edit_Tabs_SendToVendor extends Mage_Adminhtml_Block_Widget_Form
{
	private $_order = null;
	
	/**
	 * Constructeur: on charge le devis
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->setTemplate('purchase/order/edit/tab/sendtovendor.phtml');
	}
		
	/**
	 * Définit l'order
	 *
	 */
	public function setOrderId($value)
	{
		$this->_order = Mage::getModel('purchase/order')->load($value);
		return $this;
	}
	
	/**
	 * Retourne la commande
	 *
	 */
	public function getOrder()
	{
		return $this->_order;
	}
	
}