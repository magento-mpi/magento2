<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\App;

/**
 * Interface for global configs
 */
interface ConfigInterface
{
    /**
     * Configuration path
     */
    const XML_PATH_ENABLED = 'catalog/magento_catalogpermissions/enabled';

    /**
     * Check, whether permissions are enabled
     *
     * @return bool
     */
    public function isEnabled();
}
