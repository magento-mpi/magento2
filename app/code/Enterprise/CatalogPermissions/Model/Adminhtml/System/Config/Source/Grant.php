<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source for grant permission select
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Adminhtml_System_Config_Source_Grant
{
    /**
     * Retrieve Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Enterprise_CatalogPermissions_Helper_Data::GRANT_ALL            => __('Yes, for Everyone'),
            Enterprise_CatalogPermissions_Helper_Data::GRANT_CUSTOMER_GROUP => __('Yes, for Specified Customer Groups'),
            Enterprise_CatalogPermissions_Helper_Data::GRANT_NONE           => __('No')
        );
    }
}
