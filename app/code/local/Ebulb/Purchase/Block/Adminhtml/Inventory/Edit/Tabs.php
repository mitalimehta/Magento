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
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    { 
        parent::__construct();
        $this->setId('inventory_info_tabs');   
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('purchase')->__('Product Information'));  
    }

    protected function _beforeToHtml()
    {
        
        $this->addTab('form_section2', array(
            'label'     => Mage::helper('purchase')->__('Vendors'),
            'class'     => 'ajax',
            'url'       => $this->getUrl('*/*/order', array('_current' => true)),
            //'content'   => $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml(),
         ));
         
         
         $this->addTab('form_section3', array(
            'label'     => Mage::helper('purchase')->__('Stock Movement'),
            'class'     => 'ajax',
            'url'       => $this->getUrl('*/*/stockmovement', array('_current' => true)),
            //'content'   => $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml(),
         )); 
         
         $this->addTab('form_section4', array(
            'label'     => Mage::helper('purchase')->__('Sales Orders'),
            'class'     => 'ajax',
            'url'       => $this->getUrl('*/*/salesorder', array('_current' => true)),
            //'content'   => $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml(),
         )); 
         
         $this->addTab('form_section5', array(
            'label'     => Mage::helper('purchase')->__('Purchase Orders'),
            'class'     => 'ajax',
            'url'       => $this->getUrl('*/*/purchasesorder', array('_current' => true)),
            //'content'   => $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml(),
         )); 
        
 
    
        $this->_updateActiveTab();
        Varien_Profiler::stop('inventory/tabs');
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }  
}
