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
if (version_compare(PHPUnit_Extensions_Selenium2TestCase::VERSION, '1.3.3', '<')) {
    throw new PHPUnit_Framework_Exception('PHPUnit_Selenium 1.3.3 (or later) is required.');
}
define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . '/../../../../'));
define('SELENIUM_TESTS_SCREENSHOTDIR', realpath(SELENIUM_TESTS_BASEDIR . '/var/screenshots'));
define('SELENIUM_TESTS_LOGS', realpath(SELENIUM_TESTS_BASEDIR . '/var/logs'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(SELENIUM_TESTS_BASEDIR . '/framework'),
            realpath(SELENIUM_TESTS_BASEDIR . '/testsuite'),
            realpath(SELENIUM_TESTS_BASEDIR . '/../../../lib'),
            get_include_path(),
        )
    )
);

require_once realpath(SELENIUM_TESTS_BASEDIR . '/../../../app/autoload.php');
Mage_Selenium_TestConfiguration::getInstance();