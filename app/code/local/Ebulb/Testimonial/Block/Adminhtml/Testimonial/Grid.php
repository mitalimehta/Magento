<?php

class Ebulb_Testimonial_Block_Adminhtml_Testimonial_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  protected $_massactionBlockName = 'adminhtml/widget_grid_massactionpermission'; 
  
  public function __construct()
  {
      parent::__construct();
      $this->setId('testimonialGrid');
      $this->setDefaultSort('testimonial_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
       
      
      $collection = Mage::getModel('testimonial/testimonial')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      
      $this->addColumn('testimonial_id', array(
          'header'    => Mage::helper('testimonial')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'testimonial_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('testimonial')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  
	  $this->addColumn('testimonial_text', array(
          'header'    => Mage::helper('testimonial')->__('Testimonial'),
          'align'     =>'left',
          'index'     => 'testimonial_text',
		  'renderer'  => 'adminhtml/widget_grid_column_renderer_longtext',
		  'string_limit'  => '100',
      ));
      
     $this->addColumn('fullname', array(
          'header'    => Mage::helper('testimonial')->__('Name'),
          'align'     =>'left',
          'index'     => 'fullname',
      ));
      
      $this->addColumn('city', array(
          'header'    => Mage::helper('testimonial')->__('City'),
          'align'     =>'left',
          'index'     => 'city',
      ));
      
      $this->addColumn('state', array(
          'header'    => Mage::helper('testimonial')->__('State'),
          'align'     =>'left',
          'index'     => 'state',
      ));
      
      $this->addColumn('posted', array(
          'header'    => Mage::helper('testimonial')->__('Posted'),
          'align'     =>'left',
          'index'     => 'posted',
          'renderer' => new Ebulb_Orderprint_Block_Sales_Order_Fdate(),
      ));

	 /*$this->addColumn('filename', array(
          'header'    => Mage::helper('testimonial')->__('Imagen'),
          'align'     =>'left',
          'index'     => 'filename',
      ));*/
			
	/*	$this->addColumn('fechaini',
            array(
                'header'=>Mage::helper('testimonial')->__('Inicio'),
                'index'=>'fechaini',
                'gmtoffset' => false,
                'type'=>'date'
        ));

        $this->addColumn('fechafin',
            array(
                'header'=>Mage::helper('testimonial')->__('Fin'),
                'index'=>'fechafin',
                'gmtoffset' => false,
                'type'=>'date'
        ));*/
			  
      $this->addColumn('approved', array(
          'header'    => Mage::helper('testimonial')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'approved',
          'type'      => 'options',
          'options'   => array(
              1 => 'Approved',
              2 => 'Declined',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('testimonial')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('testimonial')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
      		
		$this->addExportType('*/*/exportCsv', Mage::helper('testimonial')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('testimonial')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonial');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('testimonial')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('testimonial')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('testimonial/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('testimonial')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('testimonial')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
    public function getMassactionBlock(){
         $block = Mage::getBlockSingleton($this->_massactionBlockName);   
         $block->setParentBlock($this);
         $block->setType("adminhtml/widget_grid_massaction"); 
         //echo $this->getRequest()->getControllerName();;
         return $block;   
     }
     
     public function getMassactionBlockHtml()
    {  
        $block = Mage::getBlockSingleton($this->_massactionBlockName);
        
        Mage::getBlockSingleton('lightbox/admin_tasklogin')
          ->renderForm();        
          
        return $block->_toHtml();  
    }

}