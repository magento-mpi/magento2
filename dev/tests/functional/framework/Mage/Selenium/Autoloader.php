<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class, which described method for automatically calling of needed class/interface what you are trying to use,
 * which has not been defined yet. Simple autoloader implementation.
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Autoloader
{
    /**
     * Registers the autoloader handler
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Autoload handler implementation. Performs calling of class/interface, which has not been defined yet
     *
     * @param string $className Class name to be loaded, e.g. Mage_Selenium_TestCase
     *
     * @return boolean True if the class was loaded, otherwise False.
     */
    public static function autoload($className)
    {
        $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $className)));
        $classFile = $classFile . '.php';
        $path = explode(PATH_SEPARATOR, ini_get('include_path'));
        foreach ($path as $possiblePath) {
            if (file_exists($possiblePath . DIRECTORY_SEPARATOR . $classFile)) {
                include_once $classFile;
                if (class_exists($className, false)) {
                    return true;
                }
            }
        }
        return false;
    }
}
