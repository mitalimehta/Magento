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
class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Contact extends Mage_Adminhtml_Block_Widget_Grid
{
    static  $_guidSequence = 1;
    private $_guid = null;
    private $_parentId;   
    static  $_rowCount = 0;  
    
    public function __construct()
    {   
        parent::__construct();
        $this->setId('ContactGrid');
        $this->setDefaultSort('vendor_contact_id');
        $this->setDefaultDir('DESC');  
        $this->setSaveParametersInSession(true);
        $this->_parentTemplate = $this->getTemplate();
        //$this->setTemplate('catalogupgrade/list.phtml');          
        //$this->setEmptyText('No records');
        $this->setEmptyText('No records');
        //$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
       
       /*$this->setId('customer_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);*/ 
    }
    
    public function setParentId($parentId)
    {
        $this->_parentId = $parentId;
        return $this;
    }
    
    public function getParentId()
    {  
        return $this->_parentId;
    }

    protected function _prepareCollection()
    { 
        $id     = $this->getRequest()->getParam('id');
       
        $collection = Mage::getModel('purchase/contact')->getCollection();  
        
        $collection
            ->getSelect()
            ->columns("IF( `main_table`.ext1 <> '' , concat(`main_table`.telephone1,' Ext - ',`main_table`.ext1), `main_table`.telephone1 ) as telephone")
            ->columns("main_table.email as email_id");
        
        if($id != ''){
            $collection
                ->getSelect()
                ->where("`main_table`.vendor_id = ".$id);
            Mage::register('contact_vendor_id',$id); 
        }
            
        else if($this->getParentId()){
            $collection
                ->getSelect()
                ->where("`main_table`.vendor_id = ".$this->getParentId());    
            Mage::register('contact_vendor_id',$this->getParentId()); 
        } 
            
        /*echo $collection
            ->getSelect()->__toString();exit;*/    
        
       
            
        $this->setCollection($collection);  
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('vendor_contact_id', array(
            'header'    => Mage::helper('purchase')->__('ID'),
            'width'     => '50px',
            'index'     => 'vendor_contact_id',
            'type'  => 'number',
            'sortable'      => false,    
        ));
        
        $this->addColumn('first_name', array(
            'header'    => Mage::helper('purchase')->__('First Name'),
            'index'     => 'first_name',
            'sortable'      => false,    
        ));
        
        $this->addColumn('last_name', array(
            'header'    => Mage::helper('purchase')->__('Last Name'),
            'index'     => 'last_name',
            'sortable'      => false,    
        ));
        
        $this->addColumn('email_id', array(
            'header'    => Mage::helper('purchase')->__('Email'),
            'width'     => '250',
            'index'     => 'email_id',
            'sortable'      => false,    
        )); 

        $this->addColumn('telephone', array(
            'header'    => Mage::helper('purchase')->__('Telephone'),
            'width'     => '150',
            'index'     => 'telephone',
            'sortable'      => false,    
        ));
        //Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Renderer_Action
        
        $this->addColumn('action', array(
            'header'=> Mage::helper('Catalog')->__('Action'),
            'index' => 'action',
            'renderer'=> 'Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Renderer_Action',
            'filter'    => false,
            'sortable'  => false,
            'align'     => 'center',
            'guid' => $this->getGuid(),
            //'ParentId' => $this->getParentId()
        ));
       
        //$this->addExportType('*/*/exportCsv', Mage::helper('purchase')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('purchase')->__('Excel XML'));
        return parent::_prepareColumns();
    }  
    
    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    
    public function setGuid($guid)
    {
            $this->_guid = $guid;
            return $this;
    }
    
    public function getGuid()
    {
        if($this->_guid == null)
           if($this->getRequest()->getParam('guid') != '')
              $this->_guid = $this->getRequest()->getParam('guid');
           else
           {
              $this->_guid = Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Contact::$_guidSequence;
              Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Contact::$_guidSequence += 1;
          }
        return $this->_guid;
    }
    
}
