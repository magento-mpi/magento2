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
define('SELENIUM_TESTS_BASEDIR', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'));
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

if (defined('SELENIUM_TESTS_INSTALLATION') && SELENIUM_TESTS_INSTALLATION === 'enabled') {
    $baseDir = realpath(__DIR__ . '/../../../../');
    $installCmd = sprintf('php -f %s --', escapeshellarg($baseDir . '/dev/shell/install.php'));
    if (defined('SELENIUM_TESTS_INSTALLATION_CLEANUP') && SELENIUM_TESTS_INSTALLATION_CLEANUP === 'enabled') {
        passthru("$installCmd --uninstall", $exitCode);
        if ($exitCode) {
            exit($exitCode);
        }
    }
    $installConfigFile = SELENIUM_TESTS_BASEDIR . '/config/install.php';
    $installConfigFile = file_exists($installConfigFile) ? $installConfigFile : "$installConfigFile.dist";
    $installConfig = require($installConfigFile);
    $installOptions = isset($installConfig['install_options']) ? $installConfig['install_options'] : array();

    /* Install application */
    if ($installOptions) {
        foreach ($installOptions as $optionName => $optionValue) {
            $installCmd .= sprintf(' --%s %s', $optionName, escapeshellarg($optionValue));
        }
        passthru($installCmd, $exitCode);
        if ($exitCode) {
            exit($exitCode);
        }
    }
}

/* Unset declared global variables to release PHPUnit from maintaining their values between tests */
unset($installCmd, $installConfigFile, $installConfig, $installExitCode);
