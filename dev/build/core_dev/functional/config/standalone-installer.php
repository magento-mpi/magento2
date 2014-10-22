<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
!defined('SELENIUM_TESTS_INSTALLATION') && define('SELENIUM_TESTS_INSTALLATION', 'enabled');
!defined('SELENIUM_TESTS_INSTALLATION_CLEANUP') && define('SELENIUM_TESTS_INSTALLATION_CLEANUP', 'enabled');

define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . '/..'));
define('SELENIUM_TESTS_SCREENSHOTDIR', realpath(SELENIUM_TESTS_BASEDIR . '/var/screenshots'));
define('SELENIUM_TESTS_LOGS', realpath(SELENIUM_TESTS_BASEDIR . '/var/logs'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(SELENIUM_TESTS_BASEDIR . '/framework'),
    realpath(SELENIUM_TESTS_BASEDIR . '/testsuite'),
    realpath(SELENIUM_TESTS_BASEDIR . '/../../../lib'),
    get_include_path(),
)));

require_once realpath(SELENIUM_TESTS_BASEDIR . '/../../../app/autoload.php');

//if (defined('SELENIUM_TESTS_INSTALLATION') && SELENIUM_TESTS_INSTALLATION === 'enabled') {
if (defined('SELENIUM_TESTS_INSTALLATION_CLEANUP') && SELENIUM_TESTS_INSTALLATION_CLEANUP === 'enabled') {
    $uninstallCmd = sprintf(
        'php -f %s uninstall',
        escapeshellarg(realpath(SELENIUM_TESTS_BASEDIR . '/../../../setup/index.php'))
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
$installOptionsNoValue = isset($installConfig['install_options_no_value']) ? $installConfig['install_options_no_value'] : array();

/* Install application */
if ($installOptions) {
    $installCmd = sprintf(
        'php -f %s install',
        escapeshellarg(realpath(SELENIUM_TESTS_BASEDIR . '/../../../setup/index.php'))
    );
    foreach ($installOptions as $optionName => $optionValue) {
        $installCmd .= sprintf(' --%s=%s', $optionName, escapeshellarg($optionValue));
    }
    foreach ($installOptionsNoValue as $optionName) {
        $installCmd .= sprintf(' --%s', $optionName);
    }
    passthru($installCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }

    /* Dump Database */
    $dumpCommand = "mysqldump -u{$installOptions['db_user']} -p{$installOptions['db_pass']} "
        . "{$installOptions['db_name']} -h{$installOptions['db_host']} > {$installOptions['db_name']}.sql";
    passthru($dumpCommand, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
}
//}

/* Unset declared global variables to release PHPUnit from maintaining their values between tests */
unset($installCmd, $installConfigFile, $installConfig, $installExitCode);
