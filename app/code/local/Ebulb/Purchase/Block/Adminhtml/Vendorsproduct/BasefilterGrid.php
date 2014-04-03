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
 * Adminhtml report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Purchase_Block_Adminhtml_Vendorsproduct_BasefilterGrid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_vendorSwitcherVisibility = true;



    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(false);
        $this->setVendorSwitcherVisibility(true);
    }

    protected function _prepareLayout()
    {
        $this->setChild('purchase_adminhtml_vendorsproduct_vendor_switcher',
            $this->getLayout()->createBlock('purchase/adminhtml_vendorsproduct_vendor_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('vendor'=>null)))
                ->setTemplate('purchase/vendorsproduct/vendor/switcher.phtml')
        );

        parent::_prepareLayout();
        return $this;
    }

    protected function _prepareColumns()
    {
        foreach ($this->_columns as $_column) {
            $_column->setSortable(false);
        }

        parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {

        return parent::_prepareCollection();

    }

    /**
     * Set visibility of store switcher
     *
     * @param boolean $visible
     */
    public function setVendorSwitcherVisibility($visible=true)
    {
        $this->_vendorSwitcherVisibility = $visible;
    }

    /**
     * Return visibility of store switcher
     *
     * @return boolean
     */
    public function getVendorSwitcherVisibility()
    {
        return $this->_vendorSwitcherVisibility;
    }

    /**
     * Return store switcher html
     *
     * @return string
     */
    public function getVendorSwitcherHtml()
    {

        //return $this->getChildHtml('store_switcher');
        return $this->getChildHtml('purchase_adminhtml_vendorsproduct_vendor_switcher');
    }



}
