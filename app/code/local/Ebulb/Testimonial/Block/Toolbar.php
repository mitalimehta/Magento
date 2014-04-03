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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product list toolbar
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

class Ebulb_Testimonial_Block_Toolbar extends Mage_Page_Block_Html_Pager
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('testimonial/toolbar.phtml');
    }

    public function setCollection($collection)
    {
        parent::setCollection($collection);
        return $this;
    }


    public function getAvailableLimit()
    {
    	$perPageValues = Mage::getConfig()->getNode('frontend/testimonial/per_page_values');
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        return ($perPageValues);
        return parent::getAvailableLimit();
    }

    public function getLimit()
    {
        $limits = $this->getAvailableLimit();
        if ($limit = $this->getRequest()->getParam($this->getLimitVarName())) {
            if (isset($limits[$limit])) {
                return $limit;
            }
        }
        $defaultLimit = Mage::getStoreConfig('testimonial/frontend/product_per_page');
        if ($defaultLimit != '') {
            return $defaultLimit;
        }
        $limits = array_keys($limits);
        return $limits[0];
    }
}
