<?php

namespace Magento\Composer\Helper;

class Converter {
    static function nametoVendorPackage($name)
    {
        if ($name != null && sizeof($name) > 0 && substr_count($name, "/") <= 1) {
            if(strpos($name, "_") != false) {
                return preg_replace("/_/", "/", $name, 1);
            } elseif(strpos($name, "\\") != false){
                return preg_replace("/\\\/", "/", $name, 1);
            } elseif(strpos($name, "/") != false){
                return $name;
            }
        }
        throw new \Exception("Not a valid name: $name", "1");
    }

    static function vendorPackagetoName($vendorPackage){
        if($vendorPackage != null && sizeof($vendorPackage) > 0 ){
            if(strpos($vendorPackage, "/") != false && substr_count($vendorPackage, "/") === 1){
                return str_replace("/", "_" , $vendorPackage);
            } elseif(strpos($vendorPackage, "\\") != false && substr_count($vendorPackage, "\\") === 1)
            {
                return str_replace("\\", "_" , $vendorPackage);
            }
        }
        throw new \Exception("Not a valid vendorPackage: $vendorPackage", "1");
    }
}