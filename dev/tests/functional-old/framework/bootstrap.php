<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
if (version_compare(PHPUnit_Extensions_Selenium2TestCase::VERSION, '1.3.3', '<')) {
    throw new PHPUnit_Framework_Exception('PHPUnit_Selenium 1.3.3 (or later) is required.');
}
define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . '/..'));
define('SELENIUM_TESTS_SCREENSHOTDIR', realpath(SELENIUM_TESTS_BASEDIR . '/var/screenshots'));
define('SELENIUM_TESTS_LOGS', realpath(SELENIUM_TESTS_BASEDIR . '/var/logs'));

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(SELENIUM_TESTS_BASEDIR . '/framework'),
            realpath(SELENIUM_TESTS_BASEDIR . '/testsuite'),
            realpath(SELENIUM_TESTS_BASEDIR . '/../../../lib/internal'),
            get_include_path(),
        )
    )
);

require_once realpath(SELENIUM_TESTS_BASEDIR . '/../../../app/autoload.php');
require_once 'functions.php';

if (defined('SELENIUM_TESTS_INSTALLATION') && SELENIUM_TESTS_INSTALLATION === 'enabled') {
    if (defined('SELENIUM_TESTS_INSTALLATION_CLEANUP') && SELENIUM_TESTS_INSTALLATION_CLEANUP === 'enabled') {
        $uninstallCmd = sprintf(
            'php -f %s --',
            escapeshellarg(realpath(SELENIUM_TESTS_BASEDIR . '/../../../dev/shell/uninstall.php'))
        );
        passthru($uninstallCmd, $exitCode);
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
        $installCmd = sprintf(
            'php -f %s --',
            escapeshellarg(realpath(SELENIUM_TESTS_BASEDIR . '/../../../dev/shell/install.php'))
        );
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
