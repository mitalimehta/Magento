<?php

class Ebulb_Purchase_Model_Vendor extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('purchase/vendor');
    }
    
    public function getCountryCollection()
    {
        $collection = $this->getData('country_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore();
            $this->setData('country_collection', $collection);
        }
        //echo "<pre>";print_r($collection->getData());
        return $collection;
    }
    
    public function getCountryOptions()
    {
        $options    = false;
        $useCache   = Mage::app()->useCache('config');
        if ($useCache) {
            $cacheId    = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
            $cacheTags  = array('config');
            if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                $options = unserialize($optionsCache);
            }
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            if ($useCache) {
                Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
            }
        }
        
        return $options;
    }

    public function getAddressAsText($ShowAll = true)
    {
        $return = null;
        $return = $this->getVendorCompanyName()." \n ";
        $return .= $this->getVendorName()." \n ";
        $return .= $this->getAddress1()." \n ";
        if ($this->getAddress2()){
        $return .= $this->getAddress2()." \n ";
        }
        $return .= $this->getCity().", ";
        $return .= $this->getState().' '.$this->getZipcode()." \n ";
        if ($this->getCountry() != '')
            $return .= Mage::getModel('directory/country')->loadByCode($this->getCountry())->getName()." \n ";
        if ($ShowAll)
        {
            $return .= 'Fax : '.$this->getFax()." \n ";
            $return .= 'Email : '.$this->getEmail();
        }
        return $return;
    }    
}