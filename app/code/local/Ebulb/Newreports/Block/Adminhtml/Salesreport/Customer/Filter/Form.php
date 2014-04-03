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

class Ebulb_Newreports_Block_Adminhtml_Salesreport_Customer_Filter_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Report type options
     */
    protected $_reportTypeOptions = array();

    /**
     * Add report type option
     *
     * @param string $key
     * @param string $value
     * @return Mage_Adminhtml_Block_Report_Filter_Form
     */
    public function addReportTypeOption($key, $value)
    {
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

        $fieldset->addField('report_type', 'select', array(
            'name'      => 'report_type',
            'options'   => $this->_reportTypeOptions,
            'label'     => Mage::helper('reports')->__('Report Type'),
            'title'     => Mage::helper('reports')->__('Report Type')
        ));
        
      
       
       $field = $fieldset->addField('period_type', 'select', array(
            'label' => Mage::helper('reports')->__('Range'),
            'title' => Mage::helper('reports')->__('Range'),
       ));
       
       $field->setRenderer($this->getLayout()->createBlock('newreports/adminhtml_renderer_range'));     
       
       //echo "<pre>";print_r(Mage::getSingleton('sales/order_config')->getStatuses());echo "</pre>";
       
       /*$this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));*/
        
       $statusfield = $fieldset->addField('order_status', 'checkbox', array(
            'label' => Mage::helper('reports')->__('Range'),
            'title' => Mage::helper('reports')->__('Range'),
       ));
       $statusfield->setRenderer($this->getLayout()->createBlock('newreports/adminhtml_renderer_status'));
        
       /* $fieldset->addField('order_status', 'checkbox', array(
            'name'      => 'order_status[]',
            'values'   => Mage::getSingleton('sales/order_config')->getStatuses(),
            'label'     => Mage::helper('reports')->__('Status'),
            'title'     => Mage::helper('reports')->__('Status')
        )); */
      
        $fieldset->addField('order_number_filter', 'text', array(
            'name'      => 'order_number_filter',
            'label'     => Mage::helper('reports')->__('Order Number'),
            'title'     => Mage::helper('reports')->__('Order Number'),  
            
            //'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Order Statuses'),
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
