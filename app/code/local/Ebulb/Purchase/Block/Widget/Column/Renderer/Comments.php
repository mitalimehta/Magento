<?php

class Ebulb_Purchase_Block_Widget_Column_Renderer_Comments
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$entity = $this->getColumn()->getEntity();
    	$entity_id = $row->getId();
    	if ($this->getColumn()->getentity_id_field() != '')
    		$entity_id = $row->getData($this->getColumn()->getEntityIdField());
    	$content ='test comment'; //Mage::helper('purchase')->getEntityCommentsSummary($entity, $entity_id, true);
    	$html = '';
    	if ($content != '')
	    	$html = '<a href="#" class="lien-popup"><img src="'.$this->getSkinUrl('images/purchase/comments.gif').'"><span>'.$content.'</span></a>';
    	return $html;
    }
    
}