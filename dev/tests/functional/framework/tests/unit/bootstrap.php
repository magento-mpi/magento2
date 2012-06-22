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

define('SELENIUM_UNIT_TESTS_BASEDIR', realpath(dirname(__FILE__)));
define('SELENIUM_TESTS_LIBDIR', realpath(SELENIUM_UNIT_TESTS_BASEDIR
                                         . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_BASEDIR', realpath(SELENIUM_TESTS_LIBDIR . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_SCREENSHOTDIR',
        realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'screenshots'));
define('SELENIUM_TESTS_LOGS',
        realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'logs'));

set_include_path(implode(PATH_SEPARATOR, array(
    SELENIUM_UNIT_TESTS_BASEDIR,
    SELENIUM_TESTS_LIBDIR,
    get_include_path(),
)));

require_once 'functions.php';
require_once SELENIUM_TESTS_LIBDIR . DIRECTORY_SEPARATOR . 'Mage'
             . DIRECTORY_SEPARATOR . 'Selenium' . DIRECTORY_SEPARATOR . 'Autoloader.php';
Mage_Selenium_Autoloader::register();
