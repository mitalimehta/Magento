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
class Ebulb_Automativesearch_Block_Adminhtml_Automotivebulbs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {   
        parent::__construct();
        $this->setId('vendorGrid');     
        $this->setDefaultSort('car_manufacturer_id');
        $this->setDefaultDir('ASC');  
        $this->setSaveParametersInSession(true);
       
    }

    protected function _prepareCollection()
    { 
        $collection = Mage::getModel('automativesearch/automativesearch')->getCollection(); 
        
        $collection->getSelect()->order('car_manufacturer_id');
        $collection->getSelect()->order('car_manufacturer_year_id');
        $collection->getSelect()->order('car_manufacturer_model_id');
        $collection->getSelect()->order('car_manufacturer_type_id');
        $collection->getSelect()->order('location_id'); 
        
        $collection->getSelect() 
            ->joinLeft(array('product'=>'catalog_product_entity'),'product.entity_id = `main_table`.product_id',array('sku'=>'product.sku'));  
        
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $car_manufacturers = Mage::getModel('automativesearch/automativesearch')->getAllOptions('car_manufacturer');
       
        $this->addColumn('car_manufacturer_id', array(
            'header'    => Mage::helper('customsearch')->__('Manufacturer'),
            'width'     => '100px',
            'index'     => 'car_manufacturer_id',
            'type'      => 'options',
            'options'   => $car_manufacturers,
        ));
        
        $car_years = Mage::getModel('automativesearch/automativesearch')->getAllOptions('car_year');
        
        $this->addColumn('car_manufacturer_year_id', array(
            'header'    => Mage::helper('automativesearch')->__('Year'),
            'width'     => '70px',
            'index'     => 'car_manufacturer_year_id',
            'type'      => 'options',
            'options'   => $car_years,
        ));
       
       $car_models = Mage::getModel('automativesearch/automativesearch')->getAllOptions('car_model');     
       
       $this->addColumn('car_manufacturer_model_id', array(
            'header'    => Mage::helper('automativesearch')->__('Model'),
            'width'     => '120px',
            'index'     => 'car_manufacturer_model_id',
            'type'      => 'options',
            'options'   => $car_models,
        ));
       
       $car_types = Mage::getModel('automativesearch/automativesearch')->getAllOptions('car_type');     
       
       $this->addColumn('car_manufacturer_type_id', array(
            'header'    => Mage::helper('automativesearch')->__('Type'),
            'width'     => '120px',
            'index'     => 'car_manufacturer_type_id',
            'type'      => 'options',
            'options'   => $car_types,
        ));
        
       $car_location = Mage::getModel('automativesearch/automativesearch')->getAllOptions('car_bulb_location');     
       
       $this->addColumn('location_id', array(
            'header'    => Mage::helper('automativesearch')->__('Location'),
            'width'     => '120px',
            'index'     => 'location_id',
            'type'      => 'options',
            'options'   => $car_location,
        ));
        
        $this->addColumn('sku', array(
            'header'    => Mage::helper('automativesearch')->__('SKU'),
            'width'     => '120px',
            'index'     => 'sku', 
        ));
       
     
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('automativesearch')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('automativesearch')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        //$this->addExportType('*/*/exportCsv', Mage::helper('automativesearch')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('automativesearch')->__('Excel XML'));
        
        return parent::_prepareColumns();
    } 

    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('feed_id');
        $this->getMassactionBlock()->setFormFieldName('automotivesearch');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('automativesearch')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('automativesearch')->__('Are you sure?')
        ));
       
        return $this;
    }
  
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',array('id'=>$row->getId()));
    }
    
}
