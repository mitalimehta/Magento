<?php

class Ebulb_Purchase_Block_Adminhtml_Vendor_Edit_Renderer_Telephone extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface 
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $ext1         = $element->getForm()->getElement('ext1hdn')->getValue();
        $telephone2   = $element->getForm()->getElement('telephone2hdn')->getValue();
        $ext2         = $element->getForm()->getElement('ext2hdn')->getValue();
       
        $html = '<tr>';
        $html.= '<td class="label"><label>Telephone 1</label></td>';
        $html.= '<td class="value">
                 <input type="text" id="_vendortelephone1" name="telephone1" class="required-entry input-text" value="'.$element->getValue().'" />
                 </td> 
                 <td class="value">
                 Ext. <input type="text" id="_vendorext1" name="ext1" value="'.$ext1.'" style="width:130px; border-width:1px; border-style:solid; border-color:#aaa #c8c8c8 #c8c8c8 #aaa; background:#fff; font:12px arial, helvetica, sans-serif;" />
                 </td>
                ';
        $html.= '</tr>'."\n";
        
        $html.= '<tr>';
        $html.= '<td class="label"><label>Telephone 2</label></td>';
        $html.= '<td class="value">
                 <input type="text" id="_vendortelephone2" name="telephone2" class="input-text" value="'.$telephone2.'" />
                 </td> 
                 <td class="value">
                 Ext. <input type="text" id="_vendorext2" name="ext2" value="'.$ext2.'" style="width:130px; border-width:1px; border-style:solid; border-color:#aaa #c8c8c8 #c8c8c8 #aaa; background:#fff; font:12px arial, helvetica, sans-serif;" />
                 </td>
                ';
        $html.= '</tr>'."\n";  
        
        return $html;   
    }
}

?>
