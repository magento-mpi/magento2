<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Report\Filter\Form;

/**
 * Sales Adminhtml report filter form order
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Order extends \Magento\Sales\Block\Adminhtml\Report\Filter\Form
{
    /**
     * Preparing form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof \Magento\Framework\Data\Form\Element\Fieldset) {

            $fieldset->addField(
                'show_actual_columns',
                'select',
                array(
                    'name' => 'show_actual_columns',
                    'options' => array('1' => __('Yes'), '0' => __('No')),
                    'label' => __('Show Actual Values')
                )
            );
        }

        return $this;
    }
}
