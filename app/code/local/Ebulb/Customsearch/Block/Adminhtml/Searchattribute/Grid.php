<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Customsearch_Block_Adminhtml_Searchattribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {   
        parent::__construct();
        $this->setId('vendorGrid');     
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);
       
    }

    protected function _prepareCollection()
    { 
        $collection = Mage::getModel('customsearch/searchattributeentity')->getCollection(); 
      
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customsearch')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
       
        $this->addColumn('type_label', array(
            'header'    => Mage::helper('customsearch')->__('Search Name'),
            'index'     => 'type_label'
        ));
        
        $statuses = Mage::getModel('customsearch/searchattributeentity')->getstatusIds(); 
        
        $this->addColumn('enabled', array(
            'header'    => Mage::helper('customsearch')->__('Status'),
            'width'     => '100px', 
            'index'     => 'enabled',
            'type'  => 'options',
            'options' => $statuses,
        ));
        
        /*$this->addColumn('email', array(
            'header'    => Mage::helper('customsearch')->__('Search Type'),
            'width'     => '250',
            'index'     => 'email'
        )); 

        $this->addColumn('telephone', array(
            'header'    => Mage::helper('customsearch')->__('Attbirute Name'),
            'width'     => '150',
            'index'     => 'telephone'
        )); */

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customsearch')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customsearch')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customsearch')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customsearch')->__('Excel XML'));
        return parent::_prepareColumns();
    } 

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customsearch');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('purchase')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('customsearch')->__('Are you sure?')
        ));
        
        $statuses = Mage::getModel('customsearch/searchattributeentity')->getstatusIds(); 
        
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('customsearch')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('customsearch')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
    
}
