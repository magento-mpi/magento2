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
 * Assign order status to order state form
 */
class Magento_Adminhtml_Block_Sales_Order_Status_Assign_Form extends Magento_Adminhtml_Block_Widget_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('order_status_state');
    }

    /**
     * Prepare form fields
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form   = new Magento_Data_Form(array(
            'id'        => 'edit_form',
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('Magento_Sales_Helper_Data')->__('Assignment Information')
        ));

        $statuses = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Status_Collection')
            ->toOptionArray();
        array_unshift($statuses, array('value' => '', 'label' => ''));

        $states = Mage::getSingleton('Magento_Sales_Model_Order_Config')->getStates();
        $states = array_merge(array('' => ''), $states);

        $fieldset->addField('status', 'select',
            array(
                'name'      => 'status',
                'label'     => Mage::helper('Magento_Sales_Helper_Data')->__('Order Status'),
                'class'     => 'required-entry',
                'values'    => $statuses,
                'required'  => true,
            )
        );

        $fieldset->addField('state', 'select',
            array(
                'name'      => 'state',
                'label'     => Mage::helper('Magento_Sales_Helper_Data')->__('Order State'),
                'class'     => 'required-entry',
                'values'    => $states,
                'required'  => true,
            )
        );

        $fieldset->addField('is_default', 'checkbox',
            array(
                'name'      => 'is_default',
                'label'     => Mage::helper('Magento_Sales_Helper_Data')->__('Use Order Status As Default'),
                'value'     => 1,
            )
        );


        $form->setAction($this->getUrl('*/sales_order_status/assignPost'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
