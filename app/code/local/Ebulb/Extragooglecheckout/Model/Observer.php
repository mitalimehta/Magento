<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Google Checkout Event Observer
 *
 * @category   Mage
 * @package    Mage_GoogleCheckout
 */
class Ebulb_Extragooglecheckout_Model_Observer extends Mage_GoogleCheckout_Model_Observer
{
    
    public function salesOrderShipmentTrackSaveAfter(Varien_Event_Observer $observer)
    {
        Mage::helper('extragooglecheckout')->log("=== Check Googlecheckout Order in track event?=== ".date("F j, Y, g:i a"));
        $track = $observer->getEvent()->getTrack();
        $order = $track->getShipment()->getOrder();

        Mage::helper('extragooglecheckout')->log("=== order_id   =".$order->getId()."=== ");
        Mage::helper('extragooglecheckout')->log("=== track_num  =".$track->getNumber()."=== ");
        Mage::helper('extragooglecheckout')->log("=== pay_method =".$order->getPayment()->getMethod()."=== ");
        if (($order->getPayment()->getMethod()=='extragooglecheckout') || ($order->getPayment()->getMethod()=='googlecheckout')) {
            Mage::helper('extragooglecheckout')->log("=== Yes, begin track process=== ");
            Mage::getModel('googlecheckout/api')
                ->setStoreId($order->getStoreId())
                ->deliver($order->getExtOrderId(), $track->getCarrierCode(), $track->getNumber());
            
        }else {
            Mage::helper('extragooglecheckout')->log("=== No, it is not Googlecheckout Order.=== ");
            return;
        
        }

    }
    
    
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {

        Mage::helper('extragooglecheckout')->log("=== Check Googlecheckout Order in shippment event ?=== ".date("F j, Y, g:i a"));
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();

        if (($order->getPayment()->getMethod()=='extragooglecheckout') || ($order->getPayment()->getMethod()=='googlecheckout')) {
            Mage::helper('extragooglecheckout')->log("=== begin shipment process=== ");
            $items = array();

            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItemId()) {
                    continue;
                }
                $items[] = $item->getSku();
            }

            if ($items) {
                Mage::getModel('googlecheckout/api')
                    ->setStoreId($order->getStoreId())
                    ->shipItems($order->getExtOrderId(), $items);
            }
        }else {
            return;
        
        }

    }    
    
}
