<?php

class Ebulb_Newreports_Adminhtml_StatisticsController extends Mage_Adminhtml_Controller_action
{ 
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';
        $this->loadLayout()
            ->_setActiveMenu('report/items') 
            ->_addBreadcrumb(Mage::helper('newreports')->__('Reports'), Mage::helper('newreports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('newreports')->__('Sales'), Mage::helper('newreports')->__('Sales'));
        return $this;
    }
    
    public function _initReportAction($blocks)
    {
        
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }
        
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
       
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                if($key == 'from')
                    Mage::register('report_filter_from_date',$requestData['from']);  
                if($key == 'to') 
                    Mage::register('report_filter_to_date',$requestData['to']);
            }
        }  
       
        $dateRangearray = array();
        foreach ($requestData as $key => $value) { 
            
            if($key == 'period_type'){   
                if($requestData['period_type'] == 'cu'){
                    $dateRangearray = Mage::helper('newreports')->getDateRange($requestData['period_type'],$requestData['from'],$requestData['to']);    
                    $requestData['from']    = $dateRangearray['from']; 
                    $requestData['to']      = $dateRangearray['to']; 
                }
                else{
                    $dateRangearray = Mage::helper('newreports')->getDateRange($requestData['period_type']);    
                    $requestData['from']    = $dateRangearray['from']; 
                    $requestData['to']      = $dateRangearray['to'];   
                }
            }
        }
                        
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();
        
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                
                $params->setData($key, $value);
                
                if($key == 'product_sku_filter')
                    Mage::register('report_filter_product_sku',$requestData['product_sku_filter']);  
                if($key == 'product_name_filter') 
                    Mage::register('report_filter_product_name',$requestData['product_name_filter']);
                if($key == 'period_type') 
                    Mage::register('report_filter_period_type',$requestData['period_type']); 
                if($key == 'order_number_filter')
                    Mage::register('report_filter_order_number',$requestData['order_number_filter']);     
                if($key == 'order_status')
                    Mage::register('report_filter_order_status',$requestData['order_status']);
                
                if($key == 'sales_person')
                    Mage::register('report_filter_sales_person',$requestData['sales_person']);  
             
                    
            }
        } 
        
        foreach ($blocks as $block) {
            if ($block) { 
                $block->setPeriodType($params->getData('period_type'));  
                $block->setFilterData($params);
            }
        }
         
        return $this;
    }
    
    public function homepagelinksAction() {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_Statistics_Homepagelinks.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.statistics.homepagelinks.filter.form');
        
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        
        $this->renderLayout();
    }
    
    protected function _showLastExecutionTime($flagCode, $refreshCode)
    {
        $flag = Mage::getModel('reports/flag')->setReportFlagCode($flagCode)->loadSelf();
        $updatedAt = ($flag->hasData())
            ? Mage::app()->getLocale()->storeDate(
                0, new Zend_Date($flag->getLastUpdate(), Varien_Date::DATETIME_INTERNAL_FORMAT), true
            )
            : 'undefined';

        $refreshStatsLink = $this->getUrl('*/*/refreshstatistics');
        $directRefreshLink = $this->getUrl('*/*/refreshRecent', array('code' => $refreshCode));
        
        //Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('Last updated: %s. To refresh last day\'s <a href="%s">statistics</a>, click <a href="%s">here</a>', $updatedAt, $refreshStatsLink, $directRefreshLink));
        return $this;
    }
    
     /**
     * Export sales report grid to CSV format
     */
    public function exporthomepagelinksCsvAction()
    {
        $fileName   = 'link_statistics.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_statistics_homepagelinks_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exporthomepagelinksExcelAction()
    {
        $fileName   = 'link_statistics.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_statistics_homepagelinks_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}