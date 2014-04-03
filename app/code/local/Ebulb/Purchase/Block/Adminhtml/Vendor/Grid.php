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
class Ebulb_Purchase_Block_Adminhtml_Vendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {   
        parent::__construct();
        $this->setId('vendorGrid');     
        $this->setDefaultSort('vendor_id');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);
       
    }

    protected function _prepareCollection()
    { 
        $collection = Mage::getModel('purchase/vendor')->getCollection(); 
        
        $collection
            ->getSelect()
            ->columns("IF( ext1 <> '' , concat(telephone1,' Ext - ',ext1), telephone1 ) as telephone");   
        
        /*echo $collection
            ->getSelect()->__toString();exit;*/
         
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('vendor_id', array(
            'header'    => Mage::helper('purchase')->__('ID'),
            'width'     => '50px',
            'index'     => 'vendor_id',
            'type'  => 'number',
        ));
       
        $this->addColumn('vendor_name', array(
            'header'    => Mage::helper('purchase')->__('Name'),
            'index'     => 'vendor_name'
        ));
        
        $this->addColumn('email', array(
            'header'    => Mage::helper('purchase')->__('Email'),
            'width'     => '250',
            'index'     => 'email'
        )); 

        $this->addColumn('telephone', array(
            'header'    => Mage::helper('purchase')->__('Telephone'),
            'width'     => '150',
            'index'     => 'telephone'
        ));

        $this->addColumn('zipcode', array(
            'header'    => Mage::helper('purchase')->__('ZIP'),
            'width'     => '90',
            'index'     => 'zipcode',
        ));

        $this->addColumn('country', array(
            'header'    => Mage::helper('purchase')->__('Country'),
            'width'     => '150',
            'type'      => 'country',
            'index'     => 'country',
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('purchase')->__('State/Province'),
            'width'     => '100',
            'index'     => 'state',
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
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    } 

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('vendor_id');
        $this->getMassactionBlock()->setFormFieldName('purchase');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('purchase')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('purchase')->__('Are you sure?')
        ));
       
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
    
}
