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
class Ebulb_Newreports_Block_Adminhtml_Renderer_Range extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface 
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {   
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        
        $period_type   = Mage::registry('report_filter_period_type');
        $from_date     = Mage::registry('report_filter_from_date');
        $to_date       = Mage::registry('report_filter_to_date');
        
        if($period_type == '')
            $period_type = "yd";    
        
        $html = '<tr>';
        $html .= '<td class="label">Range</td>';
        $html .= '<td class="value">
                    <select id="period_type" name="period_type" title="Range" class=" select" onchange="update_graph(this);"> 
                 '."\n";
                    foreach ($this->helper('adminhtml/dashboard_data')->getDatePeriods() as $_value=>$_label):
        $html .=        '<option value="'.$_value.'">'.$_label.'</option>'."\n"; 
                    endforeach;
        $html .=       '</select>       
                &nbsp; </td>
                </tr>'."\n";
                
        $html .= '<tr id="startdate" style="display:none;">';
        $html .= '<td class="label"></td>';
        $html .= '<td class="value">
                    From &nbsp;&nbsp;  
                    <input name="from" id="sales_report_from" value="'.$from_date.'" title="From" class="input-text" style="width: 110px ! important;" type="text">
                    <img src="'.$this->getSkinUrl('images/grid-cal.gif').'" alt="" class="v-middle" id="sales_report_from_trig" title="Select Date" style=""> 
                    
                        <script type="text/javascript">
                            //<![CDATA[
                                Calendar.setup({
                                    inputField: "sales_report_from",
                                    ifFormat: "%m/%e/%y",
                                    showsTime: false,
                                    button: "sales_report_from_trig",
                                    align: "Bl",
                                    singleClick : true
                                });
                            //]]>
                        </script>
                        &nbsp;&nbsp; To &nbsp;&nbsp;
                        <input name="to" id="sales_report_to" value="'.$to_date.'"  title="To" class="input-text" style="width: 110px ! important;" type="text">
                        <img src="'.$this->getSkinUrl('images/grid-cal.gif').'" alt="" class="v-middle" id="sales_report_to_trig" title="Select Date" style=""> 
                    
                        <script type="text/javascript">
                           //<![CDATA[
                                Calendar.setup({
                                    inputField: "sales_report_to",
                                    ifFormat: "%m/%e/%y",
                                    showsTime: false,
                                    button: "sales_report_to_trig",
                                    align: "Bl",
                                    singleClick : true
                                });
                            //]]>

                        </script>
                  
                &nbsp; </td>
                </tr>'."\n";
        
        /*$html .= '<tr id="enddate" style="display:none;">';
        $html .= '<td class="label">To</td>';
        $html .= '<td class="value">
                    <input name="to" id="sales_report_to" value="'.$to_date.'"  title="To" class="input-text" style="width: 110px ! important;" type="text">
                    <img src="'.$this->getSkinUrl('images/grid-cal.gif').'" alt="" class="v-middle" id="sales_report_to_trig" title="Select Date" style=""> 
                    
                        <script type="text/javascript">
                           //<![CDATA[
                                Calendar.setup({
                                    inputField: "sales_report_to",
                                    ifFormat: "%m/%e/%y",
                                    showsTime: false,
                                    button: "sales_report_to_trig",
                                    align: "Bl",
                                    singleClick : true
                                });
                            //]]>

                        </script>
                  
                &nbsp; </td>
                </tr>'."\n";*/
        
        $html .= '<script type="text/javascript">'."\n";
        $html .= '
            
        function update_graph(va){
            var sel = document.getElementById("period_type");
            var i = sel.options[sel.selectedIndex].value;
    
            if(i != "cu"){
                document.getElementById("startdate").style.display="none";
                //document.getElementById("enddate").style.display="none";
                document.getElementById("sales_report_from").value="";
                document.getElementById("sales_report_to").value="";
            }else{
                document.getElementById("startdate").style.display="";
                document.getElementById("enddate").style.display="";
            }
        }
        
        var rangeselect = document.getElementById("period_type");
        var rangeselectlength = rangeselect.options.length;
        
        for (var j=0; j<rangeselectlength; j++){ 
            if(rangeselect.options[j].value == "'.$period_type.'")
               rangeselect.options[j].selected=true;    
        }
             
        '."\n";
        $html .= '</script>'."\n";
        
        if($period_type == 'cu'){
            $html .= '<script type="text/javascript">'."\n";
            $html .= '
                document.getElementById("startdate").style.display="";
                //document.getElementById("enddate").style.display="";    
            '."\n";
            $html .= '</script>'."\n";    
        } 
      
        return $html;
    }

}
