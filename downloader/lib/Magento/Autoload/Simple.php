<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Autoload
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Autoload_Simple
{
    private static $_instance;

    public static function instance()
    {
        if (!self::$_instance) {
            $class = __CLASS__;
            self::$_instance = new $class();
        }
        return self::$_instance;
    }

    public static function register()
    {
        spl_autoload_register(array(self::instance(), 'autoload'));
    }

    public function autoload($class)
    {
        $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)));
        $classFile.= '.php';
        @include $classFile;
    }

}
