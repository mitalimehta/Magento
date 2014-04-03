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
class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    { 
        parent::__construct();
        $this->setId('vendor_info_tabs');   
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('purchase')->__('Vendor Information'));  
    }

    protected function _beforeToHtml()
    {
       
        $this->addTab('form_section1', array(
          'label'     => Mage::helper('purchase')->__('Vendor Information'),
          'title'     => Mage::helper('purchase')->__('Vendor Information'),
          'content'   => $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_form')->toHtml(),
          'active'    => Mage::registry('current_vendor')->getId() ? false : true
        ));
        
        if (Mage::registry('current_vendor')->getId()) {
            
            $contactCount = 0;
            $gridBlock = $this->getLayout()
                              ->createBlock('purchase/adminhtml_vendor_edit_tab_contact')
                              ->setParentId(Mage::registry('current_vendor')->getId()) 
                              ->setTemplate('purchase/contactlist.phtml');
                                                                             
            $content   = $gridBlock->toHtml();
            $this->addTab('form_section2', array(
                'label'     => Mage::helper('purchase')->__('Contacts'),
                'title'     => Mage::helper('purchase')->__('Contacts'),
                'content'   => $content
            )); 
            
            $gridproductBlock = $this->getLayout()
                              ->createBlock('purchase/adminhtml_vendor_edit_tab_product')
                              ->setParentId(Mage::registry('current_vendor')->getId()) 
                              ->setTemplate('purchase/productlist.phtml');
                                                                             
            $productcontent   = $gridproductBlock->toHtml();
            $this->addTab('form_section3', array(
                'label'     => Mage::helper('purchase')->__('Products'),
                'title'     => Mage::helper('purchase')->__('Products'),
                'content'   => $productcontent
            ));
            
            
            $this->addTab('form_section4', array(
                    'label'     => Mage::helper('purchase')->__('Orders'),
                    'class'     => 'ajax',
                    'url'       => $this->getUrl('*/*/order', array('_current' => true)),
                    //'content'   => $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml(),
                 ));
            
            /*$this->addTab('form_section4', array(
              'label'     => Mage::helper('purchase')->__('Orders'),
              'title'     => Mage::helper('purchase')->__('Orders'),
              'content'   => $this->getLayout()->createBlock('purchase/adminhtml_vendor_edit_tab_order')->toHtml(),
            )); */
            
        }

    
        $this->_updateActiveTab();
        Varien_Profiler::stop('vendor/tabs');
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
