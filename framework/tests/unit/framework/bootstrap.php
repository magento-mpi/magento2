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

define('SELENIUM_TESTS_BASEDIR', realpath(SELENIUM_UNIT_TESTS_BASEDIR . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_FWDIR', realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'framework'));

set_include_path(implode(PATH_SEPARATOR, array(
    SELENIUM_UNIT_TESTS_BASEDIR,
    SELENIUM_TESTS_FWDIR,
    get_include_path(),
)));

require_once SELENIUM_TESTS_FWDIR . '/functions.php';
require_once 'Mage/Autoloader.php';
Mage_Autoloader::register();
