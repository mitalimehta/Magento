<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ebulb_Purchase_Adminhtml_ToolsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Display massstockeditor grid
     *
     */
    public function massstockeditorAction()
    { 
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save mass stocks
     *
     */
    public function MassStockSaveAction()
    {
        //collect data
        $productidstr         =  $this->getRequest()->getParam('product_id');
        $productArray         = explode(",",$productidstr);
        
        $dataArray            = array();
        
        $stringBinNo          = $this->getRequest()->getParam('binNo');
        $stringCost           = $this->getRequest()->getParam('Cost');
        $stringAvgCost        = $this->getRequest()->getParam('avgCost');
        $stringStockMini      = $this->getRequest()->getParam('stockmini');
        $stringStockMax       = $this->getRequest()->getParam('stockmax');
        $stringStock          = $this->getRequest()->getParam('stock'); 
                             
        $t_BinNo = explode('####@@@@####', $stringBinNo);
        foreach($t_BinNo as $item)
        {
            if ($item != '')
            { 
                $t = explode('####~~~~####', $item);  
                $dataArray[$t[0]]['binno'] = $t[1];
            }
        }
        
        $t_Cost = explode('####@@@@####', $stringCost);
        foreach($t_Cost as $item)
        {
            if ($item != '')
            { 
                $t = explode('####~~~~####', $item);  
                $dataArray[$t[0]]['cost'] = $t[1];
            }
        }
        
        $t_AvgCost = explode('####@@@@####', $stringAvgCost);
        foreach($t_AvgCost as $item)
        {
            if ($item != '')
            { 
                $t = explode('####~~~~####', $item);  
                $dataArray[$t[0]]['avgcost'] = $t[1];
            }
        }
        
        $t_stockmini = explode('####@@@@####', $stringStockMini);
        foreach($t_stockmini as $item)
        {
            if ($item != '')
            { 
                $t = explode('####~~~~####', $item);  
                $dataArray[$t[0]]['stockmini'] = $t[1];
            }
        } 
        
        $t_stockmax = explode('####@@@@####', $stringStockMax);
        foreach($t_stockmax as $item)
        {
            if ($item != '')
            { 
                $t = explode('####~~~~####', $item);  
                $dataArray[$t[0]]['stockmax'] = $t[1];
            }
        }
        
        $t_stock = explode('####@@@@####', $stringStock);
        foreach($t_stock as $item)
        {
            if ($item != '')
            {  
                $t = explode('####~~~~####', $item);
                $dataArray[$t[0]]['totalqty'] = $t[1];
            }
        } 
       
        foreach($productArray as $key=>$productid){
            
            $_product = Mage::getModel('catalog/product')->load($productid); 
            if ($_product->getId())
            {
               $_product->setwarehouse_bin_number($dataArray[$productid]['binno'])
                        ->setCost($dataArray[$productid]['cost'])
                        ->setAverageCost($dataArray[$productid]['avgcost'])
                          ;
                    
               $_product->save();
               
               $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productid);
               if($stockItem->getId()){
                   
                   $pre_total_qty        = $stockItem->gettotal_qty();
                   $stockmovement        = $dataArray[$productid]['totalqty'] - $pre_total_qty;
                   $sales_order_qty      = Mage::getModel('purchase/salesorderqty')->getSalesOrderQty($productid);
                   $available_qty        = $dataArray[$productid]['totalqty'] - $sales_order_qty;
                   
                   $current_user         = Mage::getSingleton('admin/session')->getUser()->getusername();
                   if($stockmovement != 0){
                        $_stockmovement  = Mage::getModel('purchase/stockmovement');
                        $_stockmovement->setData('doc_id','----- NA -----')
                                       ->setData('product_id',$productid)
                                       ->setData('product_qty',$stockmovement)
                                       ->setData('comments','Adjustment [ '.$current_user.' ]')
                                        ; 
                       $_stockmovement->save();
                   }
                   
                   $stockItem->settotal_qty($dataArray[$productid]['totalqty']);
                   $stockItem->setqty($available_qty);
                   $stockItem->setmin_qty($dataArray[$productid]['stockmini']);
                   $stockItem->setmax_qty($dataArray[$productid]['stockmax']);
                   
                   
                   $stockItem->save();     
               } 
            }               
        }
        //exit;
        //process stock
        /*$t_stock = explode(',', $stringStock);
        //print_r($t_stock);
        foreach($t_stock as $item)
        {
            if ($item != '')
            {
                //retrieve data
                $t = explode('-', $item);                         
                $productId = $t[0];
                $qty = $t[1];
                //load stockitem and save
                $product  = Mage::getModel('catalog/product')->load($productId);
                if($product->getMasterSku() || $product->getMasterChildItems()){
                   if($product->getMasterSku())
                      $product  = getModel('catalog/product')->loadByAttribute("sku",$product->getMasterSku());
                   $child = $product->getMasterChildItems();
                   $productId = $product->getId();
                   foreach($child as $c){
                       $stockItem = mage::getModel('cataloginventory/stock_item')->loadByProduct($c->getId());
                       if ($stockItem->getId() && $stockItem->getqty() != $qty)
                           $stockItem->setqty($qty)->save();
                   }
                }
                $stockItem = mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                if($stockItem->getId() && $stockItem->getqty() != $qty)
                   $stockItem->setqty($qty)->save();
            }
        } 
        
        //process stock mini
        $t_stockMini = explode(',', $stringStockMini);
        foreach($t_stockMini as $item)
        {
            if ($item != '')
            {
                //retrieve data
                $t = explode('-', $item);
                $productId = $t[0];
                $qtyMini = $t[1];
                //load stockitem and save
                $product  = Mage::getModel('catalog/product')->load($productId);
                if($product->getMasterSku() || $product->getMasterChildItems()){
                   if($product->getMasterSku())
                      $product  = getModel('catalog/product')->loadByAttribute("sku",$product->getMasterSku());
                   $child = $product->getMasterChildItems();
                   $productId = $product->getId();
                   foreach($child as $c){
                       $stockItem = mage::getModel('cataloginventory/stock_item')->loadByProduct($c->getId());
                       if ($stockItem->getId())
                           $stockItem->setnotify_stock_qty($qtyMini)->setuse_config_notify_stock_qty(0)->save();
                   }
                }
                $stockItem = mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
                if ($stockItem->getId())
                {
                   $stockItem->setnotify_stock_qty($qtyMini)->setuse_config_notify_stock_qty(0)->save();
                }
            }
        }
        
        //process stock mini
        $t_BinNo = explode('####', $stringBinNo);
        foreach($t_BinNo as $item)
        {
            if ($item != '')
            {
                //retrieve data
                $t = explode('^^^^', $item);
                $productId = $t[0];
                $binNo = $t[1];
                //load stockitem and save
                $product  = Mage::getModel('catalog/product')->load($productId);
                if($product->getMasterSku() || $product->getMasterChildItems()){
                   if($product->getMasterSku())
                      $product  = getModel('catalog/product')->loadByAttribute("sku",$product->getMasterSku());
                   $child = $product->getMasterChildItems();
                   $productId = $product->getId();
                   foreach($child as $c){
                       $stockItem = mage::getModel('catalog/product')->load($c->getId());
                       if ($stockItem->getId())
                           $stockItem->setwarehouse_bin_number($binNo)->save();
                   }
                }
                $stockItem = mage::getModel('catalog/product')->load($productId);
                if ($stockItem->getId())
                {
                   $stockItem->setwarehouse_bin_number($binNo)->save();
                }
            }
        }*/
    }
}