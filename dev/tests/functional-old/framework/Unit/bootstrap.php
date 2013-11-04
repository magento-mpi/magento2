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

define('SELENIUM_UNIT_TESTS_BASEDIR', realpath(__DIR__));
define('SELENIUM_TESTS_BASEDIR', realpath(
    SELENIUM_UNIT_TESTS_BASEDIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_FWDIR', realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'framework'));

define('SELENIUM_TESTS_SCREENSHOTDIR', realpath(
    SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'screenshots'));
define('SELENIUM_TESTS_LOGS', realpath(
    SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'logs'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(SELENIUM_TESTS_FWDIR),
    realpath(SELENIUM_UNIT_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'testsuite'),
    get_include_path()
)));

require_once realpath(SELENIUM_TESTS_BASEDIR . '/../../../app/autoload.php');
require_once 'functions.php';
