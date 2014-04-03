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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * REgion field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebulb_Purchase_Model_Renderer_Region extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Country region collections
     *
     * array(
     *      [$countryId] => Varien_Data_Collection_Db
     * )
     *
     * @var array
     */
    static protected $_regionCollections;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>'."\n";

        $countryId = false;
        if ($country = $element->getForm()->getElement('country')) {
            $countryId = $country->getValue();
        }

        $regionCollection = false;
        if ($countryId) {
            if (!isset(self::$_regionCollections[$countryId])) {
                self::$_regionCollections[$countryId] = Mage::getModel('directory/country')
                    ->setId($countryId)
                    ->getLoadedRegionCollection()
                    ->toOptionArray();
            }
            $regionCollection = self::$_regionCollections[$countryId];
        }

        $regionId = $element->getForm()->getElement('state')->getValue();

        $htmlAttributes = $element->getHtmlAttributes();
        foreach ($htmlAttributes as $key => $attribute) {
            if ('type' === $attribute) {
                unset($htmlAttributes[$key]);
                break;
            }
        }
     
        $regionHtmlName = $element->getName();  
        $regionIdHtmlName = str_replace('region', 'state', $regionHtmlName);
        $regionHtmlId = $element->getHtmlId();
        $regionIdHtmlId = str_replace('region', 'state', $regionHtmlId);

        if ($regionCollection && count($regionCollection) > 0) { 
            $elementClass = $element->getClass();
            $html.= '<td class="label">'.$element->getLabelHtml().'</td>';
            $html.= '<td class="value">';

            $html .= '<select id="' . $regionIdHtmlId . '" name="' . $regionIdHtmlName . '" '
                 . $element->serialize($htmlAttributes) .'>' . "\n";
            foreach ($regionCollection as $region) {
                $selected = ($regionId==$region['value']) ? ' selected="selected"' : '';
                $html.= '<option value="'.$region['value'].'"'.$selected.'>'.$region['label'].'</option>';
            }
            $html.= '</select>' . "\n";

            $html .= '<input type="hidden" name="' . $regionHtmlName . '" id="' . $regionHtmlId . '" value=""/>';

            $html.= '</td>';
            $element->setClass($elementClass);
        } else {
            $element->setClass('input-text');
            $html.= '<td class="label"><label for="'.$element->getHtmlId().'">'
                . $element->getLabel()
                . ' <span class="required" style="display:none">*</span></label></td>';

            $element->setRequired(false);
            $html.= '<td class="value">';
            $html .= '<input id="' . $regionHtmlId . '" name="' . $regionHtmlName
                 . '" value="' . $element->getEscapedValue() . '" ' . $element->serialize($htmlAttributes) . "/>" . "\n";
            $html .= '<input type="hidden" name="' . $regionIdHtmlName . '" id="' . $regionIdHtmlId . '" value=""/>';
            $html .= '</td>'."\n";
        } 
        
        /*if ($country = $element->getForm()->getElement('country')) {
            $countryId = $country->getValue();
        }
        else {
            return $element->getDefaultHtml();
        }

        $regionId = $element->getForm()->getElement('region_id')->getValue();

        $html = '<tr>';
        $element->setClass('input-text');
        $html.= '<td class="label">'.$element->getLabelHtml().'</td><td class="value">';
        $html.= $element->getElementHtml();

        $selectName = str_replace('region', 'region_id', $element->getName());
        $selectId   = $element->getHtmlId().'_id';
        $html.= '<select id="'.$selectId.'" name="'.$selectName.'" class="select required-entry" style="display:none">';
        $html.= '<option value="">'.Mage::helper('customer')->__('Please select').'</option>';
        $html.= '</select>';
       
        
        
        $html.= '</td></tr>'."\n";  */
        
        $html.= '<script type="text/javascript">'."\n";
        $html.= 'new regionUpdater("'.$country->getHtmlId().'", "'.$element->getHtmlId().'", "'.$regionHtmlId.'", '.$this->helper('directory')->getRegionJson().');'."\n";
        $html.= '</script>'."\n";
        
        $html.= '</tr>'."\n";
        
        return $html;
    }
}
