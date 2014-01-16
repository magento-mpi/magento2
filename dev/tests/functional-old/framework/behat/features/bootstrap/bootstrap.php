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
define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . '/..' . '/..' . '/..' . '/../'));
define('SELENIUM_TESTS_SCREENSHOTDIR',
        realpath(SELENIUM_TESTS_BASEDIR . '/var/screenshots'));
define('SELENIUM_TESTS_LOGS',
        realpath(SELENIUM_TESTS_BASEDIR . '/var/logs'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(SELENIUM_TESTS_BASEDIR . '/framework'),
    realpath(SELENIUM_TESTS_BASEDIR . '/testsuite'),
    get_include_path(),
)));

require_once realpath(SELENIUM_TESTS_BASEDIR . '/../../../app/autoload.php');
require_once 'functions.php';

Mage_Selenium_TestConfiguration::getInstance();

//Mage_Listener_EventListener::autoAttach(SELENIUM_TESTS_BASEDIR
//                                            . implode('/', array('', 'framework', 'Mage', 'Listener', 'Observers', '*.php')));
//Mage_Testlink_Listener::registerObserver('Mage_Testlink_Annotation');
