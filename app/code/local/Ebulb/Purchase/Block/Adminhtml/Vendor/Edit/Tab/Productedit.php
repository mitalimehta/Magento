<?php

class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Productedit extends Mage_Adminhtml_Block_Widget_Form
{
    
    private $_product = null;
    private $_upgradeId = null;
    private $_guid = null;
    private $_parentId = null;
    private $_productsku = null;
    private $_productname= null;
    
    /**
     * Constructeur: on charge
     *
     */
    public function __construct()
    {
        parent::__construct();
        $upgrade_id = Mage::app()->getRequest()->getParam('upgrade_id', false);    

    }
    
    public function setParentId($parentId)
    {
        $this->_parentId = $parentId;
        return $this;
    
    } 
    
    public function getParentId()
    {
        if(!$this->_parentId && $this->_product->getParentId())
           $this->setParentId($this->_product->getParentId()) ; 
        return $this->_parentId;
        
    }
    
    public function loadProduct($productId)
    { 
        if ($productId != ''){
            $this->_product = Mage::getModel('purchase/product')->load($productId);                    
            $this->setParentId($this->_product->getVendorId());
           
            /*$product = Mage::getModel("catalog/product")->load($this->_product->getProductId());
            if($product){
               $this->_productname = $product->getName();
               $this->_productsku  = $product->getSku();   
            }*/
            
        }
    
    }
    
    public function setGuid($guid)
    {
        $this->_guid = $guid;
        return $this;
    }
    
    public function getGuid()
    {
        return $this->_guid;
    }
    
    public function getProduct()
    {
        if ($this->_product == null)
            $this->_product = Mage::getModel('purchase/product');
            
        return $this->_product;
    }
    
    public function getTitle()
    {
        if (!$this->getUpgrade()->getUpgradeId())
            return $this->__('New Upgrade');
        else
            return $this->__('Edit Upgrade');
    }
    
}