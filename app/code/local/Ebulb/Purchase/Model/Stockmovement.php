<?php

class Ebulb_Purchase_Model_Stockmovement  extends Mage_Core_Model_Abstract
{
    
    /*****************************************************************************************************************************
    * ***************************************************************************************************************************
    * Constructeur
    *
    */
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/stockmovement');
    }
    

    public function getStockmovementType($type = ''){
        
        $types = $this->getTypes();
        return $types[$type];  
    }
    
    public function loadByProduct($ProductId)
    {
        $collection = $this->getCollection()->addFilter('product_id', $ProductId);
        return $collection;
    }
    
    public function getTypes()
    {
        $return = array();
        $return['purchaseorder'] = 'Purchase Order Received';
        $return['salesordership'] = 'Sales Order Shipment';
        /*$return['rma'] = 'rma';        
        $return['donation'] = 'donation';        
        $return['lost'] = 'lost/broken';        
        $return['loan'] = 'loan';        
        $return['return'] = 'Customer Return'; */
        $return['adjustment'] = 'Adjustment';
        $return['creditmemo'] = 'Creditmemo';
        
        return $return;
    }
    
    public function getTypeCoef($type)
    {
        $return = 0;
        switch($type)
        {
            case 'purchaseorder':
                $return = 1;
                break;
            case 'order':
                $return = -1;
                break;
            case 'rma':
                $retour = -1;
                break;
            case 'donation':
                $return = -1;
                break;
            case 'lost':
                $return = -1;
                break;
            case 'loan':
                $return = -1;
                break;
            case 'return':
                $return = 1;
                break;
            case 'adjustment':
                $return = 1;
                break;
            case 'creditmemo':
                $return = 1;
                break;
        }
        return $return;
    }
    
}
