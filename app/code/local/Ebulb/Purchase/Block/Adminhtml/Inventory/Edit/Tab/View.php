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
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Purchase_Block_Adminhtml_Inventory_Edit_Tab_View
 extends Mage_Adminhtml_Block_Template
 implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_product; 
     
    protected $_stockItem;  

   public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('current_product');
        }
        return $this->_product;
    }
    
    public function getStockItem(){
       
        if (!$this->_stockItem) {
            $this->_stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($this->getProduct()->getId());
        }
        return $this->_stockItem;
    }

    public function getAvailableQuantity()
    {  
        return round($this->getStockItem()->getQty());
    }

    public function getTotalQuantity()
    {
        return round($this->getStockItem()->getTotalQty());
    }
    
    public function getPOQuantity()
    {
        return round($this->getStockItem()->getPoQty());
    }
    
    public function getMaxQuantity()
    {
        return round($this->getStockItem()->getMaxQty());
    }
    
    public function getProductSKU(){
        return $this->getProduct()->getSku();    
    }
    
    public function getProductAverageCost()
    { 
        return round($this->getProduct()->getAverageCost(),2);
    }
    
    public function getProductCost()
    {
        return round($this->getProduct()->getCost(),2);  
    }
 
    public function getTabLabel()
    {
        return Mage::helper('purchase')->__('Inventory Summary');
    }

    public function getTabTitle()
    {
        return Mage::helper('purchase')->__('Inventory Summary');
    }

    public function canShowTab()
    {
        if (Mage::registry('current_product')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (Mage::registry('current_product')->getId()) {
            return false;
        }
        return true;
    }

}
