<?php

class Ebulb_Newreports_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $_timeperiod;
    public $_daterange = array();
    
    public function getYearRange()
    {
        return array(
            '2010'=>$this->__('2010'),          
            '2011'=>$this->__('2011'),          
            '2012'=>$this->__('2012'),          
            '2013'=>$this->__('2013'),          
            '2014'=>$this->__('2014'),          
            '2015'=>$this->__('2015')           
        
        );
    }
    
    
    public function getMonthRange()
    {
        return array(
            '1'=>$this->__('January'),          
            '2'=>$this->__('February'),          
            '3'=>$this->__('March'),          
            '4'=>$this->__('April'),          
            '5'=>$this->__('May'),          
            '6'=>$this->__('June'),           
            '7'=>$this->__('July'),           
            '8'=>$this->__('August'),           
            '9'=>$this->__('September'),           
            '10'=>$this->__('October'),           
            '11'=>$this->__('November'),           
            '12'=>$this->__('Debember')           
        
        );
    }
    
    /*
    
    function is not working in new version
    public function getDateRange($timeperiod){  
        
        $this->_timeperiod = $timeperiod;
        
        list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
                ->getDateRange($this->_timeperiod, '', '', true); 
            
        switch($this->_timeperiod){
           
            case '24h':
            case '2d':                 
            case 'yd':  
                $from = $dateStart->toString('yyyy-MM-dd HH:00');
                $to   = $dateEnd->toString('yyyy-MM-dd HH:00');   
            break;
            
            case 'wk':
            case '7d':
            case 'mo':
            case '1m':
                $from = $dateStart->toString('yyyy-MM-dd');
                $to   = $dateEnd->toString('yyyy-MM-dd');   
            break;
            
            case '1y': 
            case '2y':
                $from = $dateStart->toString('yyyy-MM');
                $to   = $dateEnd->toString('yyyy-MM');   
            break;
                        
        }
       
        $this->_daterange['from'] = $from;
        $this->_daterange['to'] = $to;
        
        return $this->_daterange;
    } */
    
    
    public function getDateRange($timeperiod, $customStart = null, $customEnd = null){  
        
       
        $this->_timeperiod = $timeperiod;
        
        if($timeperiod == 'month'){ 
            list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
                    ->getDateRange($this->_timeperiod, $customStart,$customEnd, true);     
        }
        else{
            list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
                    ->getDateRange($this->_timeperiod, '', '', true); 
        
        }   
          
        if($timeperiod == 'cu'){
           
            if($customStart)
                $customStart = new Zend_Date(Mage::getModel('core/date')->gmtDate('Y-d-m H:i:s',$customStart));
            else $customStart=0;
            if($customEnd)
                $customEnd = new Zend_Date(Mage::getModel('core/date')->gmtDate('Y-d-m H:i:s',$customEnd));
            else $customEnd=0;
            
          
        }
         
        switch($this->_timeperiod){
           
            case '24h':
            case '2d':                 
            case 'yd': 
           
                $from = $dateStart->toString('yyyy-MM-dd HH:00');
                $to   = $dateEnd->toString('yyyy-MM-dd HH:00');   
            break;
            
            case 'wk':
            case '7d':
            case 'mo':
            case '1m':
                $from = $dateStart->toString('yyyy-MM-dd HH:00');
                $to   = $dateEnd->toString('yyyy-MM-dd HH:00');   
            break;
            
            case '1y': 
            case '2y':
            case 'month':    
                $from = $dateStart->toString('yyyy-MM-dd HH:00');
                $to   = $dateEnd->toString('yyyy-MM-dd HH:00');   
            break;
                     
            case 'cu': 
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd   = $customEnd ? $customEnd : $dateEnd;
                
                $dateStart->setHour(0);
                $dateStart->setMinute(0);
                $dateStart->setSecond(0);
                $dateEnd->setHour(23);
                $dateEnd->setMinute(59);
                $dateEnd->setSecond(59);
                
                $from = $dateStart->toString('yyyy-MM-dd HH:00');
                $to   = $dateEnd->toString('yyyy-MM-dd HH:00'); 
                
            break;           
        }
        
        $this->_daterange['from'] = $from;
        $this->_daterange['to'] = $to;
        
        return $this->_daterange;
    }
}