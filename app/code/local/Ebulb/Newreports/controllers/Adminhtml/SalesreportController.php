<?php

class Ebulb_Newreports_Adminhtml_SalesreportController extends Mage_Adminhtml_Controller_action
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
                    if($requestData['period_type'] == 'month'){ 
                        $from = $requestData['rangemonth1']."-".$requestData['rangeyear1'];
                        $to  = $requestData['rangemonth2']."-".$requestData['rangeyear2']; 
                        $dateRangearray = Mage::helper('newreports')->getDateRange($requestData['period_type'],$from,$to);            
                    }
                    else{
                        $dateRangearray = Mage::helper('newreports')->getDateRange($requestData['period_type']);        
                    }
                    $requestData['from']    = $dateRangearray['from']; 
                    $requestData['to']      = $dateRangearray['to'];   
                }
            }
        }
        //echo "<pre>11";print_r($requestData); echo "</pre>";exit;
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
                    
                
               if($key == 'rangemonth1')
                    Mage::register('report_filter_rangemonth1',$requestData['rangemonth1']);
               if($key == 'rangeyear1')
                    Mage::register('report_filter_rangeyear1',$requestData['rangeyear1']);
               if($key == 'rangemonth2')
                    Mage::register('report_filter_rangemonth2',$requestData['rangemonth2']);
               if($key == 'rangeyear2')
                    Mage::register('report_filter_rangeyear2',$requestData['rangeyear2']); 
                    
               if($key == 'manufacturer')
                    Mage::register('report_filter_manufacturer',$requestData['manufacturer']); 
               
               if($key == 'vendor')
                    Mage::register('report_filter_vendor',$requestData['vendor']); 
             
                    
            }
        } 
      
        foreach ($blocks as $block) {
            if ($block) { 
                $block->setPeriodType($params->getData('period_type'));  
                $block->setFilterData($params);
            }
        }
      
     //   if(Mage::registry('report_filter_order_tax_report'))
     //       Mage::register('report_filter_order_tax_report',1);
        
        return $this;
    }
    
    public function productAction() {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Product.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.filter.form');
         
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        
        $this->renderLayout();
    }
    
    public function productbyrangeAction() {
        
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Productbyrange.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.productbyrange.filter.form');
         
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        
        $this->renderLayout();
    }
    
    public function dayAction() {
       
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Day.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.day.filter.form');
        
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        
        $this->renderLayout();
    }
    
    public function customerAction() {
       
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Customer.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.customer.filter.form');
        
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
       
        $this->renderLayout();
    }
    
    public function taxAction() {
        
        Mage::register('report_filter_order_tax_report',1);  
         
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Tax.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.tax.filter.form');
        
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
       
        $this->renderLayout();
    }
    
    public function totalsAction() {  
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Salesperson_Total.grid');
        //$filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.day.filter.form');
         
        $this->_initReportAction(array(
            $gridBlock,
            //$filterFormBlock
        ));
        
        $this->renderLayout();
    }
    
      
    
    public function salespersonAction() {
       
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');
        
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));
        
        $sales_person = 'All';
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                if($key == 'sales_person')   
                {
                    $sales_person = $value;
                }
            }
        } 
        
        //echo $sales_person;//exit;
        if($sales_person == "all")
            $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Salesperson_Total.grid');
        else
            $gridBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Salesperson.grid'); 
        //$totalBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Salesperson_Total.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.salesperson.filter.form');
        
       
        $this->_initReportAction(array(
            $gridBlock,
            //$totalBlock,
            $filterFormBlock
        ));
          
        $this->renderLayout();
    }
    
    public function shiptodayAction() {
       
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');
        
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));
         
        $shiptodayBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Shiptoday.grid');
        $shiptodaytotalBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Shiptoday_Shiptodaytotal.grid');
        $shipInsuranceBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Shiptoday_Shipinsurance.grid');
        $shipInsurancetotalBlock = $this->getLayout()->getBlock('adminhtml_Salesreport_Shiptoday_Shipinsurancetotal.grid');
        $filterFormBlock = $this->getLayout()->getBlock('adminhtml.salesreport.shiptoday.filter.form');
        
        $this->_initReportAction(array(
            $shipInsuranceBlock,
            $shipInsurancetotalBlock,
            $shiptodayBlock,
            $shiptodaytotalBlock,
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
    
    
    public function refreshRecentAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            $currentDate = Mage::app()->getLocale()->date();
            $date = $currentDate->subHour(25);
            foreach ($collectionsNames as $collectionName) {
                Mage::getResourceModel($collectionName)->aggregate($date);
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Recent statistics was successfully updated'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to refresh recent statistics'));
            Mage::logException($e);
        }

        $this->_redirectReferer('*/*/sales');
        return $this;
    }

    public function refreshLifetimeAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            foreach ($collectionsNames as $collectionName) {
                Mage::getResourceModel($collectionName)->aggregate();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Lifetime statistics was successfully updated'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to refresh lifetime statistics'));
            Mage::logException($e);
        }
        $this->_redirectReferer('*/*/sales');
        return $this;
    }
    
    public function refreshStatisticsAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Refresh Statistics'));

        $this->_initAction()
            ->_setActiveMenu('report/sales/refreshstatistics')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Refresh Statistics'), Mage::helper('adminhtml')->__('Refresh Statistics'))
            ->renderLayout();
    }
    
    
    /**
     * Retrieve array of collection names by code specified in request
     *
     * @return array
     */
    protected function _getCollectionNames()
    {
        $codes = $this->getRequest()->getParam('code');
        
        if (!$codes) {
            throw new Exception(Mage::helper('adminhtml')->__('No report code specified'));
        }
        if(!is_array($codes)) {
            $codes = array($codes);
        }
        $aliases = array(
            'sales'     => 'newreports/report_order'
            /*'tax'     => 'tax/tax',
            'shipping'  => 'sales/report_shipping',
            'invoiced'  => 'sales/report_invoiced',
            'refunded'  => 'sales/report_refunded',
            'coupons'   => 'salesrule/rule'*/
        );
        $out = array();
        foreach ($codes as $code) {
            $out[] = $aliases[$code];
        }
        return $out;
    }

    /**
     * Export sales report grid to CSV format
     */
    public function exportproductCsvAction()
    {
        $fileName   = 'products.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_product_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportproductExcelAction()
    {
        $fileName   = 'products.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_product_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }  
    
    public function exportproductbyrangeCsvAction()
    {
        $fileName   = 'products.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_productbyrange_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }
    
     public function exportproductbyrangeExcelAction()
    {
        $fileName   = 'products.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_productbyrange_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }  
    
    
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportsaleperdayCsvAction()
    {
        $fileName   = 'salesperday.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_day_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
        
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportsaleperdayExcelAction()
    {
        $fileName   = 'salesperday.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_day_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportsalepersalespersonCsvAction()
    {
        $fileName   = 'salespersalesperson.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_salesperson_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportsalepersalespersonExcelAction()
    {
        $fileName   = 'salespersalesperson.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_salesperson_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    } 
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportsalepersalespersontotalCsvAction()
    {
        $fileName   = 'salespersalesperson.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_salesperson_total_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportsalepersalespersontotalExcelAction()
    {
        $fileName   = 'salespersalesperson.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_salesperson_total_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    } 
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportshiptodayCsvAction()
    {
        $fileName   = 'shiptoday.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportshiptodayExcelAction()
    {
        $fileName   = 'shiptoday.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportshipinsuranceCsvAction()
    {
        $fileName   = 'shipinsurance.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_Shipinsurance_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportshipinsuranceExcelAction()
    {
        $fileName   = 'shipinsurance.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_Shipinsurance_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }
    
    
     /**
     * Export sales report grid to CSV format
     */
    public function exportshiptodaytotalCsvAction()
    {
        $fileName   = 'shiptodaysummary.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_shiptodaytotal_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }
  
     
     /**
     * Export sales report grid to CSV format
     */
    public function exportshiptodaytotalExcelAction()
    {
        $fileName   = 'shiptodaysummary.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_shiptodaytotal_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }
  
  
    /**
     * Export sales report grid to Excel XML format
     */
    public function exportshipinsurancetotalExcelAction()
    {
        $fileName   = 'shipinsurancesummary.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_shipinsurancetotal_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportshipinsuarancetotalCsvAction()
    {
        $fileName   = 'shipinsurancesummary.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_shiptoday_shipinsurancetotal_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }
  
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportsalepercustomerCsvAction()
    {
        $fileName   = 'salespercustomer.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_customer_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportsalepercustomerExcelAction()
    {
        $fileName   = 'salespercustomer.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_customer_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }
    
    /**
     * Export sales report grid to CSV format
     */
    public function exportsalestaxCsvAction()
    {
        Mage::register('report_filter_order_tax_report',1);
        
        $fileName   = 'salestax.csv';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_tax_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsv());
    }

    /**
     * Export sales report grid to Excel XML format
     */
    public function exportsalestaxExcelAction()
    {
        Mage::register('report_filter_order_tax_report',1);
        
        $fileName   = 'salestax.xml';
        $grid       = $this->getLayout()->createBlock('newreports/adminhtml_salesreport_tax_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcel($fileName));
    }
	
}
