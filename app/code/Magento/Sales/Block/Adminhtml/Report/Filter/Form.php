<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Adminhtml report filter form
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Adminhtml_Report_Filter_Form extends Magento_Adminhtml_Block_Report_Filter_Form
{
    /**
     * Add fields to base fieldset which are general to sales reports
     *
     * @return Magento_Sales_Block_Adminhtml_Report_Filter_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var Magento_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Magento_Data_Form_Element_Fieldset) {

            $statuses = Mage::getModel('Magento_Sales_Model_Order_Config')->getStatuses();
            $values = array();
            foreach ($statuses as $code => $label) {
                if (false === strpos($code, 'pending')) {
                    $values[] = array(
                        'label' => __($label),
                        'value' => $code
                    );
                }
            }

            $fieldset->addField('show_order_statuses', 'select', array(
                'name'      => 'show_order_statuses',
                'label'     => __('Order Status'),
                'options'   => array(
                        '0' => __('Any'),
                        '1' => __('Specified'),
                    ),
                'note'      => __('Applies to Any of the Specified Order Statuses'),
            ), 'to');

            $fieldset->addField('order_statuses', 'multiselect', array(
                'name'      => 'order_statuses',
                'values'    => $values,
                'display'   => 'none'
            ), 'show_order_statuses');

            // define field dependencies
            if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
                $this->setChild(
                    'form_after',
                    $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Form_Element_Dependence')
                        ->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
                        ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
                        ->addFieldDependence('order_statuses', 'show_order_statuses', '1')
                );
            }
        }

        return $this;
    }
}
