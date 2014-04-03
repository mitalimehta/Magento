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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer new password field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Newreports_Block_Adminhtml_Renderer_Status extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface 
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {   
        $statusarray        = Mage::getSingleton('sales/order_config')->getStatuses();
        $statusselected     = Mage::registry('report_filter_order_status');
       
        if(count($statusselected) == 0){
            $statusselected[0] = "pending";
            $statusselected[1] = "pending_payment";
            $statusselected[2] = "processing";
            $statusselected[3] = "complete";
            $statusselected[4] = "pending_paypal";
        }
            
        $html = '<tr>';
        $html .= '<td class="label">Status</td>';
        $html .= '<td class="value">
                        <table cellpadding="2" cellspacing="2" width="500">
                    '."\n";
                    $key = 0;
                    foreach ($statusarray as $_key=>$_val):
                        
                        if($key % 3  === 0)
                            $html .=   '<tr>'."\n";
                            
                                $html .=  '<td width="33%" align="left"><input type="checkbox" name="order_status[]" title="order status" value="'.$_key.'" '."\n";
                                           if(in_array($_key,$statusselected))
                                                $checked = " checked = checked ";
                                           else
                                                $checked = ""; 
                                $html .= ' '.$checked.' >  '.$_val.' </td>'."\n"; 
       
                        if($key % 3  === 0 && $key % 3 !==0)   
                            $html .=   '</tr>'."\n";   
                           $key++;
                    endforeach;
        $html .=       '
                &nbsp; </table></td>
                </tr>'."\n";
                
        
        return $html;
    }

}
