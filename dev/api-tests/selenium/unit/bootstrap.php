<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  runner
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('SELENIUM_UNIT_TESTS_BASEDIR', realpath(dirname(__FILE__)));

define('SELENIUM_TESTS_BASEDIR', realpath(SELENIUM_UNIT_TESTS_BASEDIR . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_LIBDIR', realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'lib'));

set_include_path(implode(PATH_SEPARATOR, array(
    SELENIUM_UNIT_TESTS_BASEDIR,
    SELENIUM_TESTS_LIBDIR,
    get_include_path(),
)));

require_once 'functions.php';
require_once SELENIUM_UNIT_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'Mage'  . DIRECTORY_SEPARATOR . 'Autoloader.php';
Mage_Autoloader::register();
