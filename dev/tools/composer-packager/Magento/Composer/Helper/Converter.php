<?php

namespace Magento\Composer\Helper;

class Converter {
    static function nametoVendorPackage($name)
    {
        if ($name != null && sizeof($name) > 0 && strpos($name, "_") != false) {
            //return strtolower(str_replace("_", DIRECTORY_SEPARATOR, $name));
            return preg_replace("/_/", DIRECTORY_SEPARATOR, $name, 1);
        }
        return $name;
    }

    static function vendorPackagetoName($vendorPackage){
        if($vendorPackage != null && sizeof($vendorPackage) > 0 && strpos($vendorPackage, DIRECTORY_SEPARATOR) != false){
            return str_replace(DIRECTORY_SEPARATOR, "_" , $vendorPackage);
        }
    }
}