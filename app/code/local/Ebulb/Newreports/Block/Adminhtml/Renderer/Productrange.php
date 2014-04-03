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
class Ebulb_Newreports_Block_Adminhtml_Renderer_Productrange extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface 
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {   
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        
        $period_type   = Mage::registry('report_filter_period_type');
        $from_date     = Mage::registry('report_filter_from_date');
        $to_date       = Mage::registry('report_filter_to_date');
      
        $report_filter_rangemonth1 = Mage::registry('report_filter_rangemonth1');
        $report_filter_rangeyear1 = Mage::registry('report_filter_rangeyear1');
        $report_filter_rangemonth2 = Mage::registry('report_filter_rangemonth2');
        $report_filter_rangeyear2 = Mage::registry('report_filter_rangeyear2');
      
        if($period_type == '')
            $period_type = "yd";    
        
       
        $html = '<tr>';
        $html .= '<td class="label">Range</td>';
        $html .= '<td class="value">
                    <b>From:</b>
                    <select id="rangemonth1" name="rangemonth1" style="width:90px">
                         '."\n";
                    foreach (Mage::helper('newreports')->getMonthRange() as $_value=>$_label):   
                        if($report_filter_rangemonth1 == $_value){ 
                            $selected = "selected  = 'selected'" ;
                        }
                        else{
                            $selected = '';    
                        }
        $html .=        '<option value="'.$_value.'" '.$selected.' >'.$_label.'</option>'."\n"; 
                    endforeach;
                    
        $html .=       '</select> &nbsp;
                    <select id="rangeyear1" name="rangeyear1" title="Range" style="width:70px">
                 '."\n";
                    foreach (Mage::helper('newreports')->getYearRange() as $_value=>$_label):
                        if($report_filter_rangeyear1 == $_value){ 
                            $selected = "selected  = 'selected'" ;
                        }
                        else{
                            $selected = '';    
                        }
        $html .=        '<option value="'.$_value.'" '.$selected.' >'.$_label.'</option>'."\n"; 
                    endforeach;
        $html .=       '</select> 
                     &nbsp;
                    <b>To:</b>
                    <select id="rangemonth2" name="rangemonth2" style="width:90px">
                         '."\n";
                    foreach (Mage::helper('newreports')->getMonthRange() as $_value=>$_label):
                        if($report_filter_rangemonth2 == $_value){ 
                            $selected = "selected  = 'selected'" ;
                        }
                        else{
                            $selected = '';    
                        }
                        
        $html .=        '<option value="'.$_value.'" '.$selected.' >'.$_label.'</option>'."\n"; 
                    endforeach;
        $html .=       '</select> &nbsp;
                    <select id="rangeyear2" name="rangeyear2" title="Range" style="width:70px">
                 '."\n";
                 
                    foreach (Mage::helper('newreports')->getYearRange() as $_value=>$_label):
                        if($report_filter_rangeyear2 == $_value){ 
                            $selected = "selected  = 'selected'" ;
                        }
                        else{
                            $selected = '';    
                        }
                        
        $html .=        '<option value="'.$_value.'" '.$selected.' >'.$_label.'</option>'."\n"; 
                    endforeach;
        $html .=       '</select> 
                &nbsp; </td>
                </tr>'."\n";
        
        return $html;
    }

}
