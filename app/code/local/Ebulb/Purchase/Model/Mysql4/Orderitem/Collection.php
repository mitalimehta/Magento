<?php

class Ebulb_Purchase_Model_Mysql4_Orderitem_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/orderitem');
    }
/*    
        public function addExpressionAttributeToSelect($alias, $expression, $attribute)
    {
        // validate alias
        if (isset($this->_joinFields[$alias])) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Joint field or attribute expression with this alias is already declared.'));
        }
        if(!is_array($attribute)) {
            $attribute = array($attribute);
        }

        $fullExpression = $expression;
        // Replacing multiple attributes
        foreach($attribute as $attributeItem) {
            
            $fullExpression = str_replace('{{attribute}}', $attributeItem, $fullExpression);
            $fullExpression = str_replace('{{' . $attributeItem . '}}', $attributeItem, $fullExpression);
        }

        $this->getSelect()->columns(array($alias=>$fullExpression));

        $this->_joinFields[$alias] = array(
            'table' => false,
            'field' => $fullExpression
        );

        return $this;
    }
    
*/
}
