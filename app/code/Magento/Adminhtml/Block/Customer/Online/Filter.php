<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers online filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Customer_Online_Filter extends Magento_Backend_Block_Widget_Form_Generic
{
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $form->addField('filter_value', 'select',
                array(
                    'name' => 'filter_value',
                    'onchange' => 'this.form.submit()',
                    'values' => array(
                        array(
                            'label' => __('All'),
                            'value' => '',
                        ),

                        array(
                            'label' => __('Customers Only'),
                            'value' => 'filterCustomers',
                        ),

                        array(
                            'label' => __('Visitors Only'),
                            'value' => 'filterGuests',
                        )
                    ),
                    'no_span' => true
                )
        );

        $form->setUseContainer(true);
        $form->setId('filter_form');
        $form->setMethod('post');

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
