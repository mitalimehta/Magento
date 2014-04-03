<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Ebulb
 * @package    Ebulb_WesternUnion
 * @copyright  Copyright (c) 2008 Andrej Sinicyn
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Ebulb_Extragooglecheckout_Block_Form extends Mage_Payment_Block_Form
{

    protected $_googleCheckoutBlock = 'googlecheckout/link';
    protected $_googleCheckoutInstance = null;
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('extragooglecheckout/form.phtml');
    }
    
    
    
    
    public function getGoogleCheckout()
    {
        if(!isset($this->_googleCheckoutInstance))
        {
            $className = Mage::getConfig()->getBlockClassName($this->_googleCheckoutBlock);
            $this->_googleCheckoutInstance = new $className();               
        }
        return $this->_googleCheckoutInstance;
    }
    
    
    public function getImageUrl()
    {
        
        return $this->getGoogleCheckout()->getImageUrl();
    }
    
 

}
