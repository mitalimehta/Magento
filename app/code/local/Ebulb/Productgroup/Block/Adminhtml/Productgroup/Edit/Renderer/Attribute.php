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
class Ebulb_Productgroup_Block_Adminhtml_Productgroup_Edit_Renderer_Attribute extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface 
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {   
        $attributestr = "document.getElementById('edit_form').attributes.value";
         
        $groupid  = $this->getRequest()->getParam('id'); 
         
        $html = '<tr>';
        $html.= '<td class="label"><label>&nbsp;</label></td>';
        $html.= '<td class="value"><input type="button" id="addattribute" name="addattribute" onclick="GetAtributeValues('.$attributestr.'); return false;" value="Add" class="scalable save" />&nbsp;';
        $html.= '</tr>'."\n";
        
        $html .= '<tr>';
        $html.= '<td class="label"><label>&nbsp;</label></td>';
        $html.= '<td class="value"><div id="attributeoptions"></div>'; 
        $html.= '</tr>'."\n";
        
        $html.= '<script type="text/javascript">'."\n";
        $html.= '
        
        var attributesarray = new Array(); 
        
    function GetAtributeValues(attributestr){       
            
    "'. $storeurl =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'";
          
        //var attributestr = document.getElementById("edit_form").attributes.value;
        
        var responsearray = attributestr.split("##########"); 
        var attributeid   = responsearray[0];
        var attributecode   = responsearray[1];  
        var i ;
        
        if(attributeid!=""){
        
              for (i=0; i < attributesarray.length; i++) {
                if (attributesarray[i].toLowerCase() == attributecode.toLowerCase()) {
                    //alert("This attribute is already selected.");  
                    return false;
                }
              }  
              attributesarray.push(attributecode); 
                 
            new Ajax.Request("'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'productgroup/attribute/getattributevalues/attributeid/"+attributeid+"/attributecode/"+attributecode,
            {   asynchronous: false, 
                onComplete: showResponse
            });  
            
        }
        else{
            alert("Please select attribute"); return false;
        }
        return false;;
        
    }
    
    function showResponse(req){
        
        var responsearray = req.responseText.split("~~~~~######~~~~~######~~~~~"); 
        var attributecode = responsearray[0] ; 
        var responsestring = responsearray[1] ;  
        
        var attroptiondivtag = document.createElement("div");  
        attroptiondivtag.id  = "attributeoptions_"+attributecode;
        attroptiondivtag.innerHTML = responsestring; 
        
        document.getElementById("attributeoptions").appendChild(attroptiondivtag);
        
        return false;
        //document.getElementById("attributeoptions").innerHTML += req.responseText;  
    }
    
    function removeoptions(attributecode){  
        
        document.getElementById("attributeoptions_"+attributecode).innerHTML = "";
        
        var d = document.getElementById("attributeoptions");
        var olddiv = document.getElementById("attributeoptions_"+attributecode);
        d.removeChild(olddiv); 
        
        for(var i=0; i<attributesarray.length;i++ )
        { 
            if(attributesarray[i]==attributecode)
                attributesarray.splice(i,1); 
        } 

    }  
            
        '."\n";
        $html.= '</script>'."\n"; 
        
        
        if($this->getRequest()->getParam('id')){
            $attributesdata = Mage::getModel('productgroup/productgroup')->getattributesbygroupid($this->getRequest()->getParam('id'));
        
            //echo "<pre>";print_r($attributesdata);exit;
        
            foreach($attributesdata as $val){
                
                //echo "attributeid :: ".$val['attribute_id']."<br />";
                $attributestr = $val['attribute_id']."##########".$val['attribute_code'];
               
                //$attributeoptions = Mage::getModel('productgroup/productgroup')->getattributeoptionsbygroupid($this->getRequest()->getParam('id'),$val['attribute_id']);
                
                $html.= '';
                $html.= '<script type="text/javascript">'."\n";
                
                $html.= 'GetAtributeValues("'.$attributestr.'");'."\n";
                $html.= '
            '."\n";  
                
                $html.= '</script>'."\n";
            }   
            
            foreach($attributesdata as $val){ 
            
                $attributeoptions = Mage::getModel('productgroup/productgroup')->getattributeoptionsbygroupid($this->getRequest()->getParam('id'),$val['attribute_id']);
                
                $html.= '<script type="text/javascript">'."\n";     
                $html.= '
                    var myselect'.$val['attribute_id'].' = document.getElementById("attributeoptionsselect_'.$val['attribute_id'].'"); 
                    var selectlength = myselect'.$val['attribute_id'].'.options.length;   
                '."\n";  
                
                $html.= '</script>'."\n";
                
                foreach($attributeoptions as $option){
                
                    $optionid=$option['option_id'];    
                    
                    $html.= '<script type="text/javascript">'."\n"; 
                
                    $html.= '
                                         
                    for (var j=0; j<selectlength; j++){   
                        if(myselect'.$val['attribute_id'].'.options[j].value == "'.$optionid.'")
                           myselect'.$val['attribute_id'].'.options[j].selected=true;
                    }   
                
                    '."\n";  
                    $html.= '</script>'."\n";      
                
                }
                    //$html.= ' 
                    //'."\n";
                    //$html.= '</script>'."\n";     
            }  
        
        }
        
        return $html;
    }

}
