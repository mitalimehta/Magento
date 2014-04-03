<?php
    
class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Tab_Purchasesorder extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('inventory_purchaseorders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);   
    }
    
    protected function _prepareCollection()
    { 
        $id     = $this->getRequest()->getParam('id');
        
        $collection = Mage::getModel('purchase/order')->getCollection(); 
       
        $collection->getSelect()
            ->joinLeft(array('product'=>'po_order_item'),'product.purchase_order_id = `main_table`.order_id',array('product_id'=>'product.product_id'))
            ->where('product.product_id = '.Mage::registry('current_product')->getId());
            ;
         
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }
  
     protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header'    => Mage::helper('purchase')->__('ID'),
            'width'     => '50px',
            'index'     => 'order_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('purchase')->__('Purchase Order ID'),
            'index'     => 'increment_id', 
        ));
       
        $this->addColumn('created_date', array(
            'header'    => Mage::helper('purchase')->__('Created Date'),
            'index'     => 'created_date',
            'type'      => 'date'
        ));
        
        $this->addColumn('order_eta', array(
            'header'    => Mage::helper('purchase')->__('ETA Date'),
            'width'     => '250',
            'index'     => 'order_eta',
            'type'      => 'date'    
        )); 
        
        $this->addColumn('total', array(
            'header'=> Mage::helper('purchase')->__('Total Amount'),
            'index' => 'total',
            'renderer'  => 'Ebulb_Purchase_Block_Widget_Column_Renderer_OrderAmount',
            'align' => 'right',
            'filter'    => false,
            'sortable'  => false

        ));
        
        $this->addColumn('purchase_rep', array(
            'header'    => Mage::helper('purchase')->__('Purchase Reps.'),
            'width'     => '90',
            'index'     => 'purchase_rep',
        ));
     
        $this->addColumn('status', array(
            'header'=> Mage::helper('purchase')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('purchase/order')->getStatuses(),
            'align'    => 'right'
        ));
        
        
        
     
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('purchase')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('purchase')->__('Edit'),
                        'url'       => array('base'=> 'purchase/orders/edit'),
                        'field'     => 'po_order_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

       // $this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
       // $this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    } 
  

    public function getRowUrl($row)
    {
        return $this->getUrl('purchase/orders/edit', array('po_order_id'=>$row->getId()));
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/purchasesorder', array('_current' => true));
    }

}
