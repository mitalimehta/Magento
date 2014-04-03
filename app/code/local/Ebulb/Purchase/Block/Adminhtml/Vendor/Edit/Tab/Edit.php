<?php

class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Edit extends Mage_Adminhtml_Block_Widget_Form
{
    
    private $_contact = null;
    private $_upgradeId = null;
    private $_guid = null;
    private $_parentId = null;
    
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
        if(!$this->_parentId && $this->_contact->getParentId())
           $this->setParentId($this->_contact->getParentId()) ; 
        return $this->_parentId;
        
    }
    
    public function loadContact($contactId)
    {
        if ($contactId != ''){
            $this->_contact = Mage::getModel('purchase/contact')->load($contactId);                    
            $this->setParentId($this->_contact->getVendorId());
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
    
    public function getContact()
    {
        if ($this->_contact == null)
            $this->_contact = Mage::getModel('purchase/contact');
            
        return $this->_contact;
    }
    
    public function getTitle()
    {
        if (!$this->getUpgrade()->getUpgradeId())
            return $this->__('New Upgrade');
        else
            return $this->__('Edit Upgrade');
    }
    
}