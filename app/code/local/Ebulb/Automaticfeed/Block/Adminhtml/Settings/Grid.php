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
class Ebulb_Automaticfeed_Block_Adminhtml_Settings_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {   
        parent::__construct();
        $this->setId('settingsGrid');
        $this->setDefaultSort('feed_id');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);
       
    }

    protected function _prepareCollection()
    { 
        $collection = Mage::getModel('automaticfeed/autofeedinfo')->getCollection();  
        
       // $collection
       //     ->getSelect()
       //     ->joinLeft(array('company'=>'auto_feed_company'),'company.company_id = `main_table`.company_id',array('company_name'=>'company.company_name'));  
            
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('feed_id', array(
            'header'    => Mage::helper('automaticfeed')->__('ID'),
            'width'     => '50px',
            'index'     => 'feed_id',
            'type'  => 'number',
        ));
                                
        $collection = Mage::getModel('automaticfeed/company')->getCollection(); 
        
        $optionsarray = array(); 
        foreach($collection as $company){
            $optionsarray[$company['company_id']] = $company['company_name'];      
        }
        
        $this->addColumn('company_id', array(
            'header'    => Mage::helper('automaticfeed')->__('Company Name'),
            'index'     => 'company_id',
            'type'      =>  'options',     
            'options'   =>  $optionsarray,
        ));
        
        $stores  = Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash();
        //echo "<pre>";print_r($stores); exit;
        $this->addColumn('store_id', array(
            'header'    => Mage::helper('automaticfeed')->__('Website'),
            'index'     => 'store_id',
            'type'      =>  'options',     
            'options'   =>  $stores,  
        ));
       
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('automaticfeed')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('automaticfeed')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('automaticfeed')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('automaticfeed')->__('Excel XML'));
        return parent::_prepareColumns();
    } 

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('feed_id');
        $this->getMassactionBlock()->setFormFieldName('automaticfeed');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('automaticfeed')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('automaticfeed')->__('Are you sure?')
        ));
       
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
    
}
