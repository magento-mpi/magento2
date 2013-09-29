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
 * Sales Adminhtml report filter form order
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Report\Filter\Form;

class Order extends \Magento\Sales\Block\Adminhtml\Report\Filter\Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var \Magento\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof \Magento\Data\Form\Element\Fieldset) {

            $fieldset->addField('show_actual_columns', 'select', array(
                'name'       => 'show_actual_columns',
                'options'    => array(
                    '1' => __('Yes'),
                    '0' => __('No')
                ),
                'label'      => __('Show Actual Values'),
            ));

        }

        return $this;
    }
}
