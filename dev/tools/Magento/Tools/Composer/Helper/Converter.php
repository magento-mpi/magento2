<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Helper;

/**
 * Helper class for Converting Component Name to Vendor/Package format
 */
class Converter
{
    /**
     * Converts Name to Vendor/Package format
     *
     * @param string $name
     * @return string
     * @throws \Exception
     */
    public static function nametoVendorPackage($name)
    {
        if ($name != null && sizeof($name) > 0 && substr_count($name, "/") <= 1) {
            if (strpos($name, "/") != false) {
                return $name;
            } elseif (strpos($name, "_") != false) {
                return preg_replace("/_/", "/", $name, 1);
            } elseif (strpos($name, "\\") != false) {
                return preg_replace("/\\\/", "/", $name, 1);
            }
        }
        throw new \Exception("Not a valid name: $name", "1");
    }

    /**
     * Converts Vendor/Package to Name format
     *
     * @param string $vendorPackage
     * @return string
     * @throws \Exception
     */
    public static function vendorPackagetoName($vendorPackage)
    {
        if ($vendorPackage != null && sizeof($vendorPackage) > 0) {
            if (strpos($vendorPackage, "/") != false && substr_count($vendorPackage, "/") === 1) {
                return str_replace("/", "_", $vendorPackage);
            } elseif (strpos($vendorPackage, "\\") != false && substr_count($vendorPackage, "\\") === 1) {
                return str_replace("\\", "_", $vendorPackage);
            }
        }
        throw new \Exception("Not a valid vendorPackage: $vendorPackage", "1");
    }
}
