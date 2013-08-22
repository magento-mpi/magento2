<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source for grant permission select
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
class Magento_CatalogPermissions_Model_Adminhtml_System_Config_Source_Grant_Landing
{
    /**
     * Retrieve Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_CatalogPermissions_Helper_Data::GRANT_ALL            => __('Yes, for Everyone'),
            Magento_CatalogPermissions_Helper_Data::GRANT_CUSTOMER_GROUP => __('Yes, for Specified Customer Groups'),
            Magento_CatalogPermissions_Helper_Data::GRANT_NONE           => __('No, Redirect to Landing Page')
        );
    }
}
