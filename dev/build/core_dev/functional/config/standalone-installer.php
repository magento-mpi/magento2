<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
!defined('SELENIUM_TESTS_INSTALLATION') && define('SELENIUM_TESTS_INSTALLATION', 'enabled');
!defined('SELENIUM_TESTS_INSTALLATION_CLEANUP') && define('SELENIUM_TESTS_INSTALLATION_CLEANUP', 'enabled');

define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . '/..'));
define('SELENIUM_TESTS_SCREENSHOTDIR', realpath(SELENIUM_TESTS_BASEDIR . '/var/screenshots'));
define('SELENIUM_TESTS_LOGS', realpath(SELENIUM_TESTS_BASEDIR . '/var/logs'));

set_include_path(implode(PATH_SEPARATOR, [
    realpath(SELENIUM_TESTS_BASEDIR . '/framework'),
    realpath(SELENIUM_TESTS_BASEDIR . '/testsuite'),
    realpath(SELENIUM_TESTS_BASEDIR . '/../../../lib'),
    get_include_path(),
]));

$opt = getopt('', ['module-list-file::']);
$enableModules = [];
if (!empty($opt['module-list-file'])) {
    $moduleListFile = $opt['module-list-file'];
    // if value is undefined, bamboo will insert the variable definition literally as "${env.bamboo_...}"
    if (!preg_match('/^\$\{/', $moduleListFile)) {
        if (!is_file($moduleListFile)) {
            throw new Exception("The specified module list file does not exist: " . $moduleListFile);
        }
        $enableModules = file($moduleListFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}

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
$installConfig = require $installConfigFile;
$installOptions = isset($installConfig['install_options']) ? $installConfig['install_options'] : [];
if ($enableModules) {
    $installOptions['enable_modules'] = implode(',', $enableModules);
}
$installOptionsNoValue = isset($installConfig['install_options_no_value']) ? $installConfig['install_options_no_value'] : [];

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
