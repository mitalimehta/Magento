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
 * Adminhtml report filter form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Ebulb_Newreports_Block_Adminhtml_Salesreport_Productbyrange_Filter_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Report type options
     */
    protected $_reportTypeOptions = array(
                                            //'year' => 'Yearly',
                                          'month' => 'Monthly'
                                            );

    /**
     * Add report type option
     *
     * @param string $key
     * @param string $value                                                     
     * @return Mage_Adminhtml_Block_Report_Filter_Form
     */
    public function addReportTypeOption($key, $value)
    {
        if(count($this->_reportTypeOptions) == 0)
            $this->_reportTypeOptions[$key] = $this->__($value);
        return $this;
    }

    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/salesreport');
        $form = new Varien_Data_Form(
            array('id' => 'filter_form', 'action' => $actionUrl, 'method' => 'get')
        );
        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('reports')->__('Filter')));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('store_ids', 'hidden', array(
            'name'  => 'store_ids'
        ));

        $statuses = Mage::getModel('sales/order_config')->getStatuses();
        $values = array();
        foreach ($statuses as $code => $label) {
            if (false === strpos($code, 'pending')) {
                $values[] = array(
                    'label' => Mage::helper('reports')->__($label),
                    'value' => $code
                );
            }
        }

       // echo "<pre>";print_r($this->_reportTypeOptions);exit;
        
        $fieldset->addField('period_type', 'select', array(
            'name'      => 'period_type',
            'options'   => $this->_reportTypeOptions,
            'label'     => Mage::helper('reports')->__('Report Type'),
            'title'     => Mage::helper('reports')->__('Report Type')
        ));
        
     
       $field = $fieldset->addField('range', 'select', array(
            'label' => Mage::helper('reports')->__('Range'),
            'title' => Mage::helper('reports')->__('Range'),
       ));
       
       $field->setRenderer($this->getLayout()->createBlock('newreports/adminhtml_renderer_productrange'));     
       
        $fieldset->addField('product_sku_filter', 'text', array(
            'name'      => 'product_sku_filter',
            'label'     => Mage::helper('reports')->__('Product SKU'),
            'title'     => Mage::helper('reports')->__('Product SKU'),
            
            //'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Order Statuses'),
        ));
        
        $fieldset->addField('product_name_filter', 'text', array(
            'name'      => 'product_name_filter',
            'label'     => Mage::helper('reports')->__('Product Name'),
            'title'     => Mage::helper('reports')->__('Product Name'),
            
            //'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Order Statuses'),
        ));
        
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');  
        
        $attrubuteArray = array();  
        $attrubuteArray[]  = "---please select manufacturer---";  
        foreach ( $attribute->getSource()->getAllOptions(true,true) as $option) {
                if($option['label'] != '')
                    $attrubuteArray[$option['value']] = $option['label'];
        }
       
        $fieldset->addField('manufacturer', 'select', array(
            'name'      => 'manufacturer',
            'options'   => $attrubuteArray,
            'label'     => Mage::helper('reports')->__('Manufacturer'),
            'title'     => Mage::helper('reports')->__('Manufacturer')
        ));
        
        $vendors_array = array();
        $vendor_collection = Mage::getModel('purchase/vendor')->getCollection();
       
        $vendors_array[]  = "---please select vendor---";
        foreach($vendor_collection as $_vendor){
            $vendors_array[$_vendor['vendor_id']]    = $_vendor['vendor_name'];
        }
        
        $fieldset->addField('vendor', 'select', array(
            'name'      => 'vendor',
            'options'   => $vendors_array,
            'label'     => Mage::helper('reports')->__('Vendor'),
            'title'     => Mage::helper('reports')->__('Vendor')
        ));

        $form->addValues($this->getFilterData()->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        // define field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
            ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
            ->addFieldDependence('order_statuses', 'show_order_statuses', '1')
        );

        return parent::_prepareForm();
    }
}
