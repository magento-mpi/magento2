<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Helper;

class Converter
{
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

    public static function vendorPackagetoName($vendorPackage)
    {
        if ($vendorPackage != null && sizeof($vendorPackage) > 0 ) {
            if (strpos($vendorPackage, "/") != false && substr_count($vendorPackage, "/") === 1) {
                return str_replace("/", "_", $vendorPackage);
            } elseif (strpos($vendorPackage, "\\") != false && substr_count($vendorPackage, "\\") === 1) {
                return str_replace("\\", "_", $vendorPackage);
            }
        }
        throw new \Exception("Not a valid vendorPackage: $vendorPackage", "1");
    }
}