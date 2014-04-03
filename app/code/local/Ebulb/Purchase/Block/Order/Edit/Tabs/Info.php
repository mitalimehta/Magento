<?php

class Ebulb_Purchase_Block_Order_Edit_Tabs_Info extends Mage_Adminhtml_Block_Widget_Form
{     
	
	private $_purchaseOrder = null;
	private $_vendor = null;
	
	/**
	 * Constructeur: on charge
	 *
	 */
	public function __construct()
	{
		$this->_blockGroup = 'purchase';
        $this->_objectId = 'vendor_id';
        $this->_controller = 'order';
		
		parent::__construct();
		
		//
        $po_order_id = Mage::app()->getRequest()->getParam('po_order_id', false);	

		$this->_order = Mage::getModel('purchase/order')->load($po_order_id);
		$this->_vendor = Mage::getModel('purchase/vendor')->load($this->_order->getVendorId());

		$this->setTemplate('purchase/order/edit/tab/info.phtml');
        
	}
			
	/**
	 * Retourne l'url pour delete
	 *
	 */
	public function getDeleteUrl()
	{
		return $this->getUrl('purchase/orders/delete').'po_order_id/'.$this->getOrder()->getIncrementId();
	}
	
	/**
	 * Retourne l'objet
	 *
	 * @return unknown
	 */
	public function getOrder()
	{
		return $this->_order;
	}
	
	/**
	 * Retourne le fournisseur
	 *
	 */
	public function getVendor()
	{
		return $this->_vendor;
	}
	

    public function getStoresAsCombo($name = 'website', $value = '')
    {
       $return = '<select  id="'.$name.'" name="'.$name.'">';
        $collection = Mage::app()->getStores();
        foreach($collection as $storeId => $store)
        {
            if ($value == $storeId)
                $selected = ' selected ';
            else 
                $selected = '';
            $return .= '<option value="'.$storeId.'" '.$selected.'>'.$store->getName().'</option>';
        
        }
        $return .= '</select>';    
        return $return;
        
    }
	


	/**
	 * Return statuses as combo
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	public function getStatusAsCombo($name, $defaultValue = '')
	{
		$retour = '<select  id="'.$name.'" name="'.$name.'">';
		$statuses = $this->getOrder()->getStatuses();
		foreach($statuses as $key => $value)
		{
			if ($key == $defaultValue)
				$selected = ' selected ';
			else 
				$selected = '';
			$retour .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		}
		$retour .= '</select>';
		return $retour;
				
	}
    
    public function getPaymentTermsAsCombo($name = 'carriers', $value = '')
    {
        $retour = '<select  id="'.$name.'" name="'.$name.'">';
        $collection = explode(',', Mage::getStoreConfig('purchase/configuration/order_payment_method'));
        foreach($collection as $item)
        {
            if (strtolower($value) == strtolower($item))
                $selected = ' selected ';
            else 
                $selected = '';
            $retour .= '<option value="'.$item.'" '.$selected.'>'.$item.'</option>';
        }
        $retour .= '</select>';
        return $retour;
    }
    
    public function getShippingMethodsAsCombo($name = 'carriers', $value = '')
    {
        $retour = '<select  id="'.$name.'" name="'.$name.'">';
        $collection =  explode(',', Mage::getStoreConfig('purchase/configuration/order_carrier'));
        foreach($collection as $item)
        {
            if (strtolower($value) == strtolower($item))
                $selected = ' selected ';
            else 
                $selected = '';
            $retour .= '<option value="'.$item.'" '.$selected.'>'.$item.'</option>';
        }
        $retour .= '</select>';
        return $retour;
    }        
}
