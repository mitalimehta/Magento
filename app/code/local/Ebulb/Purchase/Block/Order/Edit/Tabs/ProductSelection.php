<?php

class Ebulb_Purchase_Block_Order_Edit_Tabs_ProductSelection extends Mage_Adminhtml_Block_Widget_Grid
{
	private $_purchaseOrder = null;
	
	public function setOrderId($value)
	{
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
        $this->setId('ProductSelection');
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

		$allowProductTypes = array();
		$allowProductTypes[] = 'simple';
		$allowProductTypes[] = 'virtual';

		$alreadyAddedProducts = array();
		/*foreach ($this->getOrder()->getProducts() as $item)
		{
			$alreadyAddedProducts[] = $item->getProductId();
		}
        */
		
		$collection = Mage::getResourceModel('catalog/product_collection')
        	->addFieldToFilter('type_id', $allowProductTypes)
            ->addAttributeToFilter('status', 1)
        	->addAttributeToSelect('name')
			->addAttributeToSelect('ordered_qty')
        	->addAttributeToSelect('reserved_qty')
        	->addAttributeToSelect('manufacturer')
        	->joinField('stock',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');

		if (count($alreadyAddedProducts) > 0)
        	$collection->addFieldToFilter('entity_id', array('nin' => $alreadyAddedProducts));

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
   /**
     * Défini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
	    $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));
            
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        
        $this->addColumn('Sku', array(
            'header'=> Mage::helper('purchase')->__('Sku'),
            'index' => 'sku',
        ));
                   
        $this->addColumn('Name', array(
            'header'=> Mage::helper('purchase')->__('Name'),
            'index' => 'name'
        ));

        $this->addColumn('Available_Qty', array(
            'header'=> Mage::helper('purchase')->__('Available Qty'),
            'type' =>'number',
            'index'	=> 'stock'
        ));

		
        $this->addColumn('Vendors', array(
            'header'=> Mage::helper('purchase')->__('Vendors'),
            'renderer' => 'Ebulb_Purchase_Block_Widget_Column_Renderer_VendorProduct',
			'filter' => 'purchase/widget_column_filter_vendorProduct',
			'index' => 'entity_id'
        ));


        $this->addColumn('qty', array(
            'header'    => Mage::helper('purchase')->__('Add Qty'),
            'name'      => 'qty',
            'type'      => 'number',
            //'index'     => 'qty',
            'width'     => '70',
            'editable'  => true,
            'edit_only' => false
        ));
        
                     
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/productSelectionGrid', array('_current'=>true, 'po_order_id' => $this->getOrder()->getId()));
    }

    public function getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', null);
        if (!is_array($products)) {
            $products = array();
        }
        return $products;
    }
}
