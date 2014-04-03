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
class Ebulb_Productgroup_Block_Adminhtml_Productgroup_Productsku_Renderer_Attribute extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface 
{
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {   
        $groupid  = $this->getRequest()->getParam('id'); 
         
        $html = ''; 
        
        if($groupid){
            
            $attributesdata = Mage::getModel('productgroup/productgroup')->getattributesbygroupid($this->getRequest()->getParam('id'));
           
            $optionscombinations = array(); 
            $counter = 0;
            $options = array();     
            
            if(count($attributesdata) > 0 ){
            
                foreach($attributesdata as $val){  
                    $attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                                ->setPositionOrder('asc')
                                                ->setAttributeFilter($val['attribute_id']) 
                                                ->setStoreFilter()
                                                ->load();
                
                    
                    foreach($attributeOptionSingle as $value) {
                        $options[$counter][$value->getOptionId()] = $value->getValue();
                    }
                    
                    $attribute_code = $val['attribute_code'];
                    $attributeoptions[$counter] = Mage::getModel('productgroup/productgroup')->getattributeoptionsbygroupid($this->getRequest()->getParam('id'),$val['attribute_id']);
                    $counter++; 
                    
                }
          
                $attributeoptionsarr = array();
                $attributeoptionscombinationarr = array();
                $counter = 0;
                
                
                foreach($attributeoptions as $key=>$val){
                    
                    $counter1 = count($attributeoptions[$key]);
                    
                    for($i=0;$i<$counter1;$i++){
                        $attributeoptionsarr[$counter][$i] = $val[$i]['option_id'];
                    }
                    $counter++;    
                }
                
                $attributeoptionscombinationarr = $this->helper('productgroup/data')->combineItems($attributeoptionsarr); 
                
                $counter = 0;
                foreach($attributeoptionscombinationarr as $key=>$val){
                    if(is_array($val))
                        $tmparr = array_reverse($val);
                    else
                        $tmparr = $val;     
                        
                    $attributeoptionscombinationarr[$counter] = $tmparr; 
                    $counter++;
                } 
                        
                $counter = 0; 
                 
                foreach($attributeoptionscombinationarr as $key=>$val){
                    
                    $html.= '<tr><td>';  
                    
                    $optionsstr = '';
                    $optionsidstr = '';
                    if(is_array($val))  
                        $optionsidstr = implode(",",$val); 
                    else
                        $optionsidstr = $val; 
                    
                    $productsku = Mage::getModel('productgroup/productgroup')->getproductskubycombination($groupid,$optionsidstr); 
                    
                    if(is_array($val)) {
                        foreach($val as $count=>$optionid)
                        {
                            if($optionsstr == '')
                                $optionsstr = $options[$count][$optionid];
                            else 
                                $optionsstr .= " - ".$options[$count][$optionid]; 
                        }
                    }
                    else{
                        $optionsstr = $options[0][$val];    
                    }
                    
                    $html.= '</td>';
                    $html.= '<td class="labeloptions"><label>'.$optionsstr.' : </label></td>';
                    $html.= '<td class="value">
                            <input type="text" class="input-text" name="productsku_'.($key+1).'" value="'.$productsku.'" onBlur="Checkproductsku(this.value,'.$groupid.','."'".base64_encode($optionsidstr)."'".'); return false;"; />
                            <input type="hidden" name="option_id_'.($key+1).'" value="'.$optionsidstr.'" /> 
                            </td>';
                    $counter++;  
                }  
                
                $html.= '<td class="value"><input type="hidden" name="totalcombinations" value="'.$counter.'" />&nbsp;</td>';  
                $html.= '</tr>'."\n"; 
                
                $html.= '<script type="text/javascript">'."\n";
                $html.= '
                
                    function Checkproductsku(productsku,groupid,optionstr){  
                       if(productsku){
                            new Ajax.Request("'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'productgroup/attribute/getproductid/productsku/"+encode_base64(productsku)+"/groupid/"+groupid+"/optionstr/"+optionstr,
                            {   asynchronous: false, 
                                onComplete: showResponse1
                            });   
                       }
                    }   
                    
                    function showResponse1(response){
                        if(response.responseText){
                            alert(response.responseText);
                            return false;
                        }
                    }   
                    
                '."\n";
                $html.= '</script>'."\n";
            } 
                return $html;
        }
    }
    
    
  
    
}
