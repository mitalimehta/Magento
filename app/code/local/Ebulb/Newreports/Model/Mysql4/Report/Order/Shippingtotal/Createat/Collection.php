<?php 


class Ebulb_Newreports_Model_Mysql4_Report_Order_Shippingtotal_Createat_Collection extends Ebulb_Newreports_Model_Mysql4_Report_Order_Day_Createat_Collection
{
    protected $_selectedColumns = array(
        
        'entity_id'                 =>  '(e.entity_id)',
        'store_id'                  =>  '(e.store_id)',
        'order_date'                =>  '(e.created_at)',
        'order_number'              =>  '(e.increment_id)',  
        'base_grand_total'          =>  'sum(e.base_grand_total)',
        'shipping_today'            =>  'sum(e.shipping_today)' ,
        'shipping_insurance'        =>  'sum(e.shipping_insurance)', 
     
    );
    
    protected function _getSelectedColumns()
    {  
                $this->_periodFormat = 'DATE(e.created_at)';
            
            $this->_selectedColumns += array('period' => $this->_periodFormat);
        //}
        return $this->_selectedColumns;
    }
    
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
        
        if (!is_null($this->_from) || !is_null($this->_to)) {
            $where = (!is_null($this->_from)) ? "e.created_at >= '{$this->_from}'" : '';
            if (!is_null($this->_to)) {
                $where .= (!empty($where)) ? " AND e.created_at <= '{$this->_to}'" : "e.created_at <= '{$this->_to}'";
            }
     
        $select = $this->getSelect() 
            ->from(array('e' => $mainTable),$columns)
            
            ->columns("(@N := @N +1) as counter")
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
        
        $select->order($sortby." ".$dir);
      
        $this->_applyStoresFilter();
         
        $this->_applyOrderStatusFilter();  
       
        /*if (!is_null($this->_from) || !is_null($this->_to)) {  
            $select->where("DATE(e.created_at) IN(?)", new Zend_Db_Expr($subQuery));
        } */
       //echo $this->_periodFormat;exit;
        if (!$this->isTotals()) {
            $select->group($this->_periodFormat);
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