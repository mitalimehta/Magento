<?php

class Ebulb_Pageclicks_Block_Adminhtml_Pageclicks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('pageclicksGrid');
     // $this->setDefaultSort('Groupid');
     // $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  { 
      $collection = Mage::getModel('pageclicks/pageclicks')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      
      $this->addColumn('entity_id', array(
          'header'    => Mage::helper('pageclicks')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'entity_id',
      ));
       
      $this->addColumn('page_title', array(
          'header'    => Mage::helper('pageclicks')->__('Title'),
          'align'     =>'left',
          'index'     => 'page_title',
      ));
      
      $this->addColumn('html_link', array(
          'header'    => Mage::helper('pageclicks')->__('HTML Link'),
          'align'     =>'left',
          'index'     => 'html_link',
      ));
 
      $this->addColumn('action',
        array(
            'header'    =>  Mage::helper('pageclicks')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('pageclicks')->__('Edit'),
                    'url'       => array('base'=> '*/*/edit'),
                    'field'     => 'id'
                    )
                ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        )); 
        
        $this->addExportType('*/*/exportCsv', Mage::helper('pageclicks')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('pageclicks')->__('XML'));     
      
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('Groupid');
        $this->getMassactionBlock()->setFormFieldName('pageclicks');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('pageclicks')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('pageclicks')->__('Are you sure?')
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