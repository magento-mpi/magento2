<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
if (version_compare(PHPUnit_Extensions_Selenium2TestCase::VERSION, '1.3.0', '<')) {
    throw new PHPUnit_Framework_Exception('PHPUnit_Selenium 1.3.0 (or later) is required.');
}
define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . '/..'));
define('SELENIUM_TESTS_SCREENSHOTDIR', realpath(SELENIUM_TESTS_BASEDIR . '/var/screenshots'));
define('SELENIUM_TESTS_LOGS', realpath(SELENIUM_TESTS_BASEDIR . '/var/logs'));

require_once SELENIUM_TESTS_BASEDIR . '/bootstrap.php';

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(SELENIUM_TESTS_BASEDIR . '/framework'),
    realpath(SELENIUM_TESTS_BASEDIR . '/testsuite'),
    realpath(SELENIUM_TESTS_BASEDIR . '/../../../lib/internal'),
    get_include_path(),
)));

require_once realpath(SELENIUM_TESTS_BASEDIR . '/../../../app/autoload.php');
