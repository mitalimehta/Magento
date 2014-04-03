<?php 


class Ebulb_Newreports_Model_Mysql4_Report_Order_Salespersontotal_Createat_Collection extends Ebulb_Newreports_Model_Mysql4_Report_Order_Day_Createat_Collection
{
    protected $_selectedColumns = array(
        
        'entity_id'                 =>  '(e.entity_id)',
        'store_id'                  =>  '(e.store_id)',
        'order_date'                =>  '(e.created_at)', 
        'total_order'               =>  'sum(e.subtotal)',  
        'discount_amount'           =>  'sum(e.discount_amount)',  
        'shipping_amt'              =>  'sum(e.shipping_amount)', 
        'order_tax'                 =>  'sum(e.tax_amount)',  
        'order_invoiced'            =>  'sum(e.base_total_invoiced)',  
        'order_refunded'            =>  'sum(e.base_total_refunded)',  
        'orderstatus'               =>  'e.status',  
        'order_rep'                 =>  'e.ch_sales_source',  
        'shipping_today'            =>  'sum(e.shipping_today)' ,
        'shipping_insurance'        =>  'sum(e.shipping_insurance)', 
        'base_grand_total'          =>  'sum(e.base_grand_total)', 
        'customer_id'               =>  'e.customer_id', 
        'margin'                   =>  'sum(e.margin)', 
        'total_cost'               =>  'sum(e.total_cost)', 
       
    );
    
    protected function _initSelect()
    { 
        if ($this->_inited) {
            return $this;
        }
        
        if(Mage::app()->getRequest()->getParam('sort'))
            $sortby  = Mage::app()->getRequest()->getParam('sort');
        else
            $sortby  = 'order_date';
            
        if(Mage::app()->getRequest()->getParam('dir'))
            $dir     = Mage::app()->getRequest()->getParam('dir'); 
        else
            $dir     = 'asc';        
         
        // START COUNTER
        $sql = 'SET @N = 0;';
        $data = mage::getResourceModel('sales/order_item_collection')->getConnection()->query($sql);        
        
        $columns = $this->_getSelectedColumns();

        $mainTable = $this->getResource()->getMainTable();   
        
        if (!is_null($this->_from) || !is_null($this->_to)) 
        {
            $where = (!is_null($this->_from)) ? "e.created_at >= '{$this->_from}'" : '';
            if (!is_null($this->_to)) {
                $where .= (!empty($where)) ? " AND e.created_at <= '{$this->_to}'" : "e.created_at <= '{$this->_to}'";
            }
        
            /********
                Commented to hide shipping cost temporarily because of speed issues
            *******/
            /*
            $subQueryshippinhcost = clone $this->getSelect();  
            $subQueryshippinhcost->from(array('si' => 'sales_shipping_info'), array('shipping_cost'=>'sum(si.ammount)')) 
                        ->where('e.increment_id = si.order_id')
                        ->where('si.void != "Y"')
                        ->group("si.order_id")
                        ;  
            */
                  
            $select = $this->getSelect() 
                ->from(array('e' => $mainTable),$columns)
                //->joinLeft(array('si'=>'sales_shipping_info'),'e.increment_id = si.order_id',array())  
                ->from(array('sfa' => 'sales_flat_order_address'),array())
                ->columns("(@N := @N +1) as counter")
                
                //->columns(" sum((  ".new Zend_Db_Expr($subQueryshippinhcost) . " )) as shipping_cost")  ///Commented to hide shipping cost temporarily because of speed issues      
                ->where(" sfa.parent_id = e.entity_id ")
                ->where(" sfa.address_type = 'billing' ")
                ->where($where)
                ->order($sortby." ".$dir)
                
                ;
          
            if(Mage::registry('report_filter_order_number')){
                $select->where(" e.increment_id like '".Mage::registry('report_filter_order_number')."%' ");     
            }
            
            if(Mage::registry('report_filter_order_status')){
                $orderStatus = Mage::registry('report_filter_order_status');
                $select->where(" e.status IN (?) ",$orderStatus);     
            } 
              
            if(Mage::registry('report_filter_sales_person'))
            {
                $salesperson = Mage::registry('report_filter_sales_person');
                $select->where(" e.ch_sales_source !=  'Direct'");                             
                if($salesperson != 'all')
                    $select->where(" e.ch_sales_source =  '".$salesperson."'");
                
                if(!Mage::app()->getRequest()->getParam('sort'))
                    $select->order(" e.ch_sales_source asc");  
            }
            
            
            $select->order($sortby." ".$dir);
          
            $this->_applyStoresFilter();
             
            $this->_applyOrderStatusFilter();  
           
           if(!$this->isTotals()){  
                $select->group('e.ch_sales_source'); 
           } 
            
        }
        else{
            $select = $this->getSelect() 
            ->from(array('e' => $mainTable),'e.entity_id')
            ->limit(1)
            ;  
            $this->_applyStoresFilter();
         
            $this->_applyOrderStatusFilter(); 
            $this->_inited = true; 
              
        }
        $this->_inited = true;
        
        return $this;
    } 
  
}


?>