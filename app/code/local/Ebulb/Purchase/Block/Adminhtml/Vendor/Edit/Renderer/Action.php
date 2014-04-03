<?php

class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{

    public function render(Varien_Object $row){ 
        Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Contact::$_rowCount += 1;                
        $counter = Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Tab_Contact::$_rowCount;  
        $parent_id = Mage::registry('contact_vendor_id');  //$this->getRequest()->getParam('id');//10; // Mage::registry('contact_vendor_id');
        //echo $parent_id;
        $url   = 'javascript:EditContact('.$row->getData('vendor_contact_id').', '.$this->getColumn()->getguid().','.$counter.');';
        $html  = '<a href="'.$url.'">'.Mage::helper('Catalog')->__('Edit').'</a>';
        $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        $url   = 'javascript:DeleteContact('.$row->getData('vendor_contact_id').', '.$this->getColumn()->getguid().','.$parent_id.');';
        $html .= '<a href="'.$url.'">'.Mage::helper('Catalog')->__('Delete').'</a>';             
        return $html; 
    }    
    
}
  
?>
