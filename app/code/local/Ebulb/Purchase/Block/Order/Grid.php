<?php

class Ebulb_Purchase_Block_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('PurchaseOrderGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setVarNameFilter('purchase_order');
        $this->setEmptyText(Mage::helper('purchase')->__('No Items Found'));
        
        $this->setDefaultSort('order_id', 'desc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * 
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {		            
        $collection = Mage::getModel('purchase/order')
        	->getCollection();
        $collection->getSelect()
            ->join(array('vendorTable' => 'po_vendor'),
			           'main_table.vendor_id=vendorTable.vendor_id');
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
                
        $this->addColumn('increment_id', array(
            'header'=> Mage::helper('purchase')->__('Purchase Order Id'),
            'index' => 'increment_id',
        ));
                   
        $this->addColumn('created_date', array(
            'header'=> Mage::helper('purchase')->__('Created Date'),
            'index' => 'created_date',
            'type'	=> 'date'
        ));

/*        
        $this->addColumn('comments', array(
            'header'=> Mage::helper('purchase')->__('Comments'),
       		'renderer'  => 'Ebulb_Purchase_Block_Widget_Column_Renderer_Comments',
            'align' => 'center',
            'entity' => 'purchase_order',
            'filter' => false,
            'sort' => false
        ));
*/                                    
        $this->addColumn('order_eta', array(
            'header'=> Mage::helper('purchase')->__('ETA Date'),
            'index' => 'order_eta',
            'type'	=> 'date'
        ));
                  
        $this->addColumn('vendor_name', array(
            'header'=> Mage::helper('purchase')->__('Vendor'),
            'index' => 'vendor_name',
        ));
                                      
        $this->addColumn('status', array(
            'header'=> Mage::helper('purchase')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('purchase/order')->getStatuses(),
            'align'	=> 'right'
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
            'header'=> Mage::helper('purchase')->__('Purchase Reps'),
            'index' => 'purchase_rep',
            'align' => 'left'
        ));
                                                                  
/*???
        $this->addColumn('po_delivery_percent', array(
            'header'=> Mage::helper('purchase')->__('Delivery %'),
            'index' => 'po_delivery_percent',
            'align' => 'center',
            'type'	=> 'number'
        ));
*/                                                                                       
        $this->addColumn('cancel_date', array(
            'header'=> Mage::helper('purchase')->__('Canceled Date'),
            'index' => 'cancel_date',
            'align' => 'left'
        ));
                             
        $this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
        
        return parent::_prepareColumns();
    }

     public function getGridUrl()
    {
        return ''; 
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    

    public function getRowUrl($row)
    {
    	return $this->getUrl('purchase/orders/edit', array())."po_order_id/".$row->getId().'/';
    }
    
    /**
     * Url pour ajouter un Custom Shipping
     *
     */
    public function getNewUrl()
    {
		return $this->getUrl('purchase/orders/new', array());
    }
    
}
