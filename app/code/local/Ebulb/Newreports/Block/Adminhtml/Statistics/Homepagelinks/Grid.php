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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Newreports_Block_Adminhtml_Statistics_Homepagelinks_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract 
{
    protected $_columnGroupBy = 'period';
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_id'); 
        //$this->setUseAjax(true);   
        $this->setCountTotals(true);
    }
    
   

    public function getResourceCollectionName()
    { 
        return 'newreports/report_statistics_homepagelinks_collection'; 
    }
    

    protected function _prepareColumns()
    {
        $this->addColumn('counter', array(
            'header'        => Mage::helper('newreports')->__('#'),
            'index'         => 'counter',
            'width'         => 50,
            'sortable'      => false, 
            'totals_label'     => '',    
            
        ));
        
        $this->addColumn('page_title', array(
            'header'           => Mage::helper('newreports')->__('Page Title'),
            'index'            => 'page_title',
            'totals_label'     => '',
            'sortable'         => true
        )); 
        
        $this->addColumn('page_link', array(
            'header'           => Mage::helper('newreports')->__('Page Link'),
            'index'            => 'page_link',
            'totals_label'     => '',
            'sortable'         => true
        ));
        
        $this->addColumn('total_count', array(
            'header'           => Mage::helper('newreports')->__('Counter'),
            'index'            => 'total_count',
            'totals_label'     => '',
            'width'            => 120,   
            'sortable'         => true
        ));  
        
        
        $this->addExportType('*/*/exporthomepagelinksCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exporthomepagelinksExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
    
   
 
}
