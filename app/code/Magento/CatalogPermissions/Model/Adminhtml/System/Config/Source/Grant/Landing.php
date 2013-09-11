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
namespace Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Grant;

class Landing
{
    /**
     * Retrieve Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\CatalogPermissions\Helper\Data::GRANT_ALL            => __('Yes, for Everyone'),
            \Magento\CatalogPermissions\Helper\Data::GRANT_CUSTOMER_GROUP => __('Yes, for Specified Customer Groups'),
            \Magento\CatalogPermissions\Helper\Data::GRANT_NONE           => __('No, Redirect to Landing Page')
        );
    }
}
