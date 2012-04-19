<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */


set_include_path(join(PATH_SEPARATOR, array(
    UNIT_ROOT . '/tests',
    UNIT_FRAMEWORK,
    UNIT_FRAMEWORK . '/_stubs',
    get_include_path()
)));


spl_autoload_register('mageAutoloader');

/**
 * Unit auto class loader
 *
 * @param string $class
 * @return void
 * @throws Magento_Exception
 */
function mageAutoloader($class)
{
    static $paths;
    if (null === $paths) {
        $paths = explode(PATH_SEPARATOR, get_include_path());
    }
    $file = str_replace('_', '/', $class) . '.php';

    foreach ($paths as $path) {
        $filename = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($filename)) {
            require_once $filename;
            return;
        }
    }
    throw new Mage_PHPUnit_Exception(
        sprintf('Class "%s" does not exist in path "%s"', $class, get_include_path()));

}
