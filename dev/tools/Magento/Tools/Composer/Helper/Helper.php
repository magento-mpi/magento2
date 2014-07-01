<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Helper;

/**
 * Helper class for some common functionalities
 */
class Helper
{
    /**
     * Converts Vendor/Package to Name format
     *
     * @param string $vendorPackage
     * @return string Name of Package
     * @throws \Exception
     */
    public static function vendorPackageToName($vendorPackage)
    {
        if ($vendorPackage != null && is_string($vendorPackage) && sizeof($vendorPackage) > 0) {
            if (strpos($vendorPackage, '/') != false && substr_count($vendorPackage, '/') === 1) {
                return str_replace('/', '_', $vendorPackage);
            } elseif (strpos($vendorPackage, '\\') != false && substr_count($vendorPackage, '\\') === 1) {
                return str_replace('\\', '_', $vendorPackage);
            }
        }
        throw new \Exception("Not a valid vendorPackage: $vendorPackage", '1');
    }

    /**
     * Gets the paths of components
     *
     * @return array
     */
    public static function getComponentsList($workingDir)
    {
        return array(
            str_replace('\\', '/', realpath($workingDir)) . '/app/code/Magento',
            str_replace('\\', '/', realpath($workingDir)) . '/app/design/adminhtml/Magento',
            str_replace('\\', '/', realpath($workingDir)) . '/app/design/frontend/Magento',
            str_replace('\\', '/', realpath($workingDir)) . '/app/i18n/Magento',
            str_replace('\\', '/', realpath($workingDir)) . '/lib/internal/Magento'
        );
    }
}
