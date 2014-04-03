<?php

class Ebulb_Purchase_Model_Mysql4_Salesorderqty extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('purchase/salesorderqty', 'item_id');
    }
    
    public function getSalesOrderQty($productid=null)
    {  
        $select = $this->_getReadAdapter()->select()
            ->from(array('item'=>$this->getTable('purchase/salesorderqty')), array('product_id'=>'item.product_id','so_qty'=>'(SUM(item.qty_ordered)-SUM(item.qty_shipped)-SUM(item.qty_refunded)-SUM(item.qty_canceled))','parent_id'=>'parent_item_id'))
            ->join(array('order'=>$this->getTable('sales/order')), 'order.entity_id=item.order_id',array())
            ->where('order.status not in ("complete","closed","canceled")')
            ->where('order.state not in ("complete","closed","canceled")')
            ->where('item.parent_item_id IS NULL')
            ->group('item.product_id');
     
        if($productid){
            $select->where('item.product_id = '.$productid);    
        }
       
       $salesorderqty  = $this->_getReadAdapter()->fetchAll($select);
      
       $simple_item_qty = 0;
       $bundle_item_qty = 0;
       if($productid && count($salesorderqty) != 0)
       { 
            $simple_item_qty = $salesorderqty[0]['so_qty'];   
       }
      
       $bundle_product_sql = $this->_getReadAdapter()->select()
                            ->from(array('item'=>$this->getTable('purchase/salesorderqty')), array('product_id'=>'item.product_id','qty_ordered'=>'item.qty_ordered','qty_canceled'=>'item.qty_canceled','parent_id'=>'parent_item_id'))
                            ->join(array('order'=>$this->getTable('sales/order')), 'order.entity_id=item.order_id',array())
                            ->where('order.status not in ("complete","closed","canceled")')
                            ->where('order.state not in ("complete","closed","canceled")')
                            ->where('item.parent_item_id IS NOT NULL')
                            ; 
                            
       if($productid){
            $bundle_product_sql->where('item.product_id = '.$productid);    
       }
       
       $bundledproductqty  = $this->_getReadAdapter()->fetchAll($bundle_product_sql); 
       
       if(count($bundledproductqty) > 0){ 
           foreach($bundledproductqty as $_product){
                $item_QtyOrdered = $_product['qty_ordered'];     
                $parent_item_sql = $this->_getReadAdapter()->select()
                                    ->from(array('item'=>$this->getTable('purchase/salesorderqty')),array('qty_ordered'=>'item.qty_ordered','qty_shipped'=>'item.qty_shipped','qty_refunded'=>'item.qty_refunded'))   
                                    ->where('item.item_id = '.$_product['parent_id'])
                                    ;  
                
               $parent_item = $this->_getReadAdapter()->fetchAll($parent_item_sql); 
                
               if(count($parent_item) > 0){   
                    $qty_rate                   = $item_QtyOrdered / $parent_item[0]['qty_ordered'];  
                    $ship_item_qty              = ($qty_rate * $parent_item[0]['qty_shipped']);  
                    $refunded_item_qty          = ($qty_rate * $parent_item[0]['qty_refunded']);  
                    
                    if($refunded_item_qty > $ship_item_qty)
                        $item_qty               = $_product['qty_ordered'] - $refunded_item_qty - $_product['qty_canceled'] ;  
                    else
                        $item_qty               = $_product['qty_ordered'] - $ship_item_qty - $_product['qty_canceled'] ;  
                    
                    $bundle_item_qty           += $item_qty ;
                    
               }                        
           }
       } 
      
       return round($bundle_item_qty + $simple_item_qty);
    
    }
}