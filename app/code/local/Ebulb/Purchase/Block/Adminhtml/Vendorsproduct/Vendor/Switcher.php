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
 * Vendor switcher block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Purchase_Block_Adminhtml_Vendorsproduct_Vendor_Switcher extends Mage_Adminhtml_Block_Template
{
    /**
     * @var array
     */
    protected $_vendorIds;

    protected $_vendorVarName = 'vendor';

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('purchase/vendorsproduct/vendor/switcher.phtml');
                  
        $this->setUseConfirm(true);
        $this->setUseAjax(true);

    }


    /**
     * Deprecated
     */
    public function getVendorCollection()
    {
        return Mage::getModel('purchase/vendor')->getCollection();
        
    }



    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, $this->_vendorVarName => null));
    }
    
    public function setSwitchUrl($url)
    {
        $this->setData('switch_url',$url);
        return $this;
    }
    
    public function setVendorVarName($varName)
    {
        $this->_vendorVarName = $varName;
        return $this;
    }

    public function getVendorId()
    {
        return $this->getRequest()->getParam($this->_vendorVarName);
    }

    public function setVendorIds($vendorIds)
    {
        $this->_vendorIds = $vendorIds;
        return $this;
    }

    public function getVendorIds()
    {
        return $this->_vendorIds;
    }



    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    /**
     * Set/Get whether the switcher should show default option
     *
     * @param bool $hasDefaultOption
     * @return bool
     */
    public function hasDefaultOption($hasDefaultOption = null)
    {
        if (null !== $hasDefaultOption) {
            $this->_hasDefaultOption = $hasDefaultOption;
        }
        return $this->_hasDefaultOption;
    }
}
