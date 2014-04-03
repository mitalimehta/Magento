<?php

class Ebulb_Productgroup_Block_Adminhtml_Productgroup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('productgroupGrid');
      $this->setDefaultSort('Groupid');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {  
      $collection = Mage::getModel('productgroup/productgroup')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      
      $this->addColumn('Groupid', array(
          'header'    => Mage::helper('productgroup')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'Groupid',
      ));
       
      $this->addColumn('title', array(
          'header'    => Mage::helper('productgroup')->__('Name'),
          'align'     =>'left',
          'index'     => 'GroupName',
      ));

 //     $this->addColumn('action1',
 //       array(
 //           'header'    =>  Mage::helper('productgroup')->__('SKUs'),
 //           'width'     => '100',
 //           'type'      => 'action',
 //           'getter'    => 'getId',
 //           'actions'   => array(
 //               array(
 //                   'caption'   => Mage::helper('productgroup')->__('SKU'),
 //                   'url'       => array('base'=> '*/*/productsku'),
 //                   'field'     => 'id'
 //                   )
 //               ),
 //           'filter'    => false,
 //           'sortable'  => false,
 //           'index'     => 'stores',
 //           'is_system' => true,
 //       )); 
      
      $this->addColumn('action',
        array(
            'header'    =>  Mage::helper('productgroup')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('productgroup')->__('Edit'),
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
        $this->setMassactionIdField('Groupid');
        $this->getMassactionBlock()->setFormFieldName('productgroup');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('productgroup')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('productgroup')->__('Are you sure?')
        ));

 //       $statuses = Mage::getSingleton('productgroup/status')->getOptionArray();

//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('productgroup')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
 //                        'class' => 'required-entry',
//                         'label' => Mage::helper('productgroup')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));   
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}