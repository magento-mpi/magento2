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
 * Enterprise Customer Data Helper
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Helper_Address extends Enterprise_Eav_Helper_Data
{
    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'customer_address';
    }

    /**
     * Return available customer address attribute form as select options
     *
     * @return array
     */
    public function getAttributeFormOptions()
    {
        return array(
            array(
                'label' => __('Customer Address Registration'),
                'value' => 'customer_register_address'
            ),
            array(
                'label' => __('Customer Account Address'),
                'value' => 'customer_address_edit'
            ),
        );
    }
}
