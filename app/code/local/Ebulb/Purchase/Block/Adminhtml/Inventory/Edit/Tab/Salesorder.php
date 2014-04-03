<?php
    
class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Tab_Salesorder extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('inventory_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);   
    }

    protected function _prepareCollection()
    { 
        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('store_id')
            ->addFieldToSelect('billing_name')
            ->addFieldToSelect('shipping_name')
            ->addFieldToSelect('status') ;
        
        $collection->getSelect()
            ->joinInner(array('product'=>'sales_flat_order_item'),'product.order_id = `main_table`.entity_id',array('product_id'=>'product.product_id'))
            ->where('product.product_id = '.Mage::registry('current_product')->getId())
            ->group('main_table.entity_id');
            ;
        
        $collection->getSelect()->where('`main_table`.status not in ('.'"complete","closed","canceled"'.')');    
        //echo $collection->getSelect()->__toString();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('customer')->__('Order #'),
            'width'     => '100',
            'index'     => 'increment_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('customer')->__('Purchase On'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'filter_index'=>'main_table.created_at',
        ));

        /*$this->addColumn('shipping_firstname', array(
            'header'    => Mage::helper('customer')->__('Shipped to First Name'),
            'index'     => 'shipping_firstname',
        ));

        $this->addColumn('shipping_lastname', array(
            'header'    => Mage::helper('customer')->__('Shipped to Last Name'),
            'index'     => 'shipping_lastname',
        ));*/
        $this->addColumn('billing_name', array(
            'header'    => Mage::helper('customer')->__('Bill to Name'),
            'index'     => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header'    => Mage::helper('customer')->__('Shipped to Name'),
            'index'     => 'shipping_name',
        ));

        $this->addColumn('grand_total', array(
            'header'    => Mage::helper('customer')->__('Order Total'),
            'index'     => 'grand_total',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('customer')->__('Bought From'),
                'index'     => 'store_id',
                'type'      => 'store',
                'filter_index'=>'main_table.store_id', 
                'store_view' => true
            ));
        } 
        
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '120px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

       
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/salesorder', array('_current' => true));
    }

}
