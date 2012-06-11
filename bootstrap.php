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
if (version_compare(PHPUnit_Runner_Version::id(), '3.6.0', '<')) {
    throw new RuntimeException('PHPUnit 3.6.0 (or later) is required.');
}
define('SELENIUM_TESTS_BASEDIR', realpath(dirname(__FILE__)));
define('SELENIUM_TESTS_SCREENSHOTDIR',
        realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'screenshots'));
define('SELENIUM_TESTS_LOGS',
        realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'logs'));

set_include_path(implode(PATH_SEPARATOR, array(
            realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'framework'),
            realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'testsuite'), //To allow load tests helper files
            get_include_path(),
        )));

require_once 'Mage/Selenium/Autoloader.php';
Mage_Selenium_Autoloader::register();

require_once 'functions.php';

Mage_Selenium_TestConfiguration::getInstance();

//Mage_Listener_EventListener::autoAttach(SELENIUM_TESTS_BASEDIR
//                                            . implode(DIRECTORY_SEPARATOR, array('', 'framework', 'Mage', 'Listener', 'Observers', '*.php')));
//Mage_Testlink_Listener::registerObserver('Mage_Testlink_Annotation');