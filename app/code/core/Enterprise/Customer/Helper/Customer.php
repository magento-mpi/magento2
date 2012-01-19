<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise Customer EAV Attributes Data Helper
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Helper_Customer extends Enterprise_Eav_Helper_Data
{
    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'customer';
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return array
     */
    public function getAttributeFormOptions()
    {
        return array(
            array(
                'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('Customer Checkout Register'),
                'value' => 'checkout_register'
            ),
            array(
                'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('Customer Registration'),
                'value' => 'customer_account_create'
            ),
            array(
                'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('Customer Account Edit'),
                'value' => 'customer_account_edit'
            ),
            array(
                'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('Admin Checkout'),
                'value' => 'adminhtml_checkout'
            ),
        );
    }
}
