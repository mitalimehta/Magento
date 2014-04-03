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
class Ebulb_Automaticfeed_Block_Adminhtml_Settings_Edit_Renderer_Attribute extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface 
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {   
        $autofeedModel = Mage::registry('current_settings');       
        
        if($autofeedModel->getId() > 0)
            $autofeedAttr  = $autofeedModel->getAttributes($autofeedModel->getId()); 
        else 
            $autofeedAttr = array();
            
        $html = ''; 
       
        $html = '<tr>';
        $html.= '<td class="label" colspan="3">';
        $html.= '<strong>Note:</strong><br />';
        $html.= '- For Product URL select -> URL Key <br />';
        $html.= '- For Image URL select   -> Base Image <br />';
        $html.= '- For Image Thumbnail select -> Thumbnail <br />';
        $html.= '- For Promo Text select -> Free Shipping <br />';
        $html.= '- For Product Category <br />';
        $html.= '  &nbsp;&nbsp;&nbsp;if Category is required to have shopping company category then select custom value and enter their category <br />';
        $html.= '  &nbsp;&nbsp;&nbsp;if Category is required to have our product category then select custom value and enter <b>"product_category"</b> <br />';
        $html.= '</td>';
        $html.= '</tr>';
        
       
        $html.= '<tr>';
        $html.= '<td class="label" colspan="3">';
        
        $html .= '<table cellspacing="4" cellpadding="4" width="100%">';
        $html .= '<tr>';
        $html .= '<td class="value">'; 
        $html .= '<label><strong>Shopping Company Attribute</strong></label>';
        $html .= '</td>';
        
        $html .= '<td class="value">'; 
        $html .= '<label><strong>Attribute</strong></label>';
        $html .= '</td>';
        
        $html .= '<td class="value"><strong>Sort Order</strong></td>';
        
        $html.= '<td class="value">
                    <button type="button" class="scalable add" onclick="addnewattribute();";>
                        <span>ADD</span>   
                    </button>
                </td> </tr> </table>';
        $html.= '</td> </tr>'."\n";
        
        $html .= '<tr>'; 
        $html.= '<td class="label" colspan="3"><div id="attributeoptions"></div></td>';
        $html.= '</tr>'."\n";
        
        
        $html.= '<script type="text/javascript">'."\n";
        $html.= '
        
        var attributesarray = new Array(); 
        var counter = 0;   
        
        function addnewattribute(company_attr,attribute_id,sort_order,custom_value)
        {       
            
            "'. $storeurl =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'";
        
            var url = "'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'automaticfeed/adminhtml_settings/getattribute/counter/"+counter;
            
            if(company_attr)
                url += "/company_attr/"+encode_base64(company_attr);   
            
            if(attribute_id)
                url += "/attribute_id/"+attribute_id;   
                
            if(sort_order)
                url += "/sort_order/"+sort_order;   
            
            if(custom_value)
                url += "/custom_value/"+custom_value;   
        
            new Ajax.Request(url,
            {   asynchronous: false, 
                onComplete: showResponse
            });  
            
            counter++;
            return false;
         }
    
        function showResponse(req){
            
            var responsestring = req.responseText ;  
            
            var attroptiondivtag = document.createElement("div");  
            attroptiondivtag.id  = "attributeoptions_"+counter;
            attroptiondivtag.innerHTML = responsestring; 
            
            document.getElementById("attributeoptions").appendChild(attroptiondivtag);
            
            return false;
            //document.getElementById("attributeoptions").innerHTML += req.responseText;  
        }
        
        function deleteattribute(countervalue){  
            
            document.getElementById("attributeoptions_"+countervalue).innerHTML = "";
            
            var d = document.getElementById("attributeoptions");
            var olddiv = document.getElementById("attributeoptions_"+countervalue);
            d.removeChild(olddiv); 
            
            for(var i=0; i<attributesarray.length;i++ )
            { 
                if(attributesarray[i]==countervalue)
                    attributesarray.splice(i,1); 
            } 

        }  
        
        function showcustomvalue(selectvalue,countervalue){
           
            if(selectvalue == 0)
                document.getElementById("custom_value_div_"+countervalue).style.display = "";
            else{
                document.getElementById("custom_value_div_"+countervalue).style.display = "none";    
                document.getElementById("custom_value_"+countervalue).value = "";    
            }
        }
            
        '."\n";
        
        if(count($autofeedAttr) > 0){
            foreach($autofeedAttr as $attr){
                
                $company_attr  = $attr['company_attr'];
                $attribute_id  = $attr['attribute_id'];
                $sort_order    = $attr['sort_order'];
                $custom_value  = $attr['custom_value'];
                
            $html.= '  
                addnewattribute("'.$company_attr.'","'.$attribute_id.'","'.$sort_order.'","'.$custom_value.'");  
                counter++;
            '."\n"; 
            }                                        
        }
        $html.= '</script>'."\n"; 
        
        return $html;
    }

}
