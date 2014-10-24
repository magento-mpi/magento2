<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/../../static/framework/Magento/TestFramework/Utility/Classes.php';
require_once __DIR__ . '/../lib/OAuth/bootstrap.php';

$testsBaseDir = dirname(__DIR__);
$testsTmpDir = "{$testsBaseDir}/tmp";
$magentoBaseDir = realpath("{$testsBaseDir}/../../../");
$integrationTestsDir = realpath("{$testsBaseDir}/../integration");

(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
    array(
        "{$testsBaseDir}/framework",
        "{$testsBaseDir}/testsuite",
        "{$testsBaseDir}/lib",
        "{$integrationTestsDir}/framework",
        "{$integrationTestsDir}/lib"
    )
);

/* Bootstrap the application */
$bootstrap = new \Magento\TestFramework\Bootstrap(
    new \Magento\TestFramework\Bootstrap\Settings($testsBaseDir, get_defined_constants()),
    new \Magento\TestFramework\Bootstrap\Environment(),
    new \Magento\TestFramework\Bootstrap\WebapiDocBlock("{$integrationTestsDir}/testsuite"),
    new \Magento\TestFramework\Bootstrap\Profiler(new \Magento\Framework\Profiler\Driver\Standard()),
    new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer()),
    $testsTmpDir
);
$bootstrap->runBootstrap();
\Magento\TestFramework\Helper\Bootstrap::setInstance(new \Magento\TestFramework\Helper\Bootstrap($bootstrap));
\Magento\TestFramework\Utility\Files::setInstance(new \Magento\TestFramework\Utility\Files($magentoBaseDir));

/** Magento installation */
if (defined('TESTS_MAGENTO_INSTALLATION') && TESTS_MAGENTO_INSTALLATION === 'enabled') {
    if (defined('TESTS_CLEANUP') && TESTS_CLEANUP === 'enabled') {
        $unInstallCmd = sprintf('php -f %s --', escapeshellarg(realpath($testsBaseDir . '/../../shell/uninstall.php')));
        passthru($unInstallCmd, $exitCode);
        if ($exitCode) {
            exit($exitCode);
        }
        echo $unInstallCmd . "\n";
    }
    $installConfigFile = $testsBaseDir . '/config/install.php';
    $installConfigFile = file_exists($installConfigFile) ? $installConfigFile : "{$installConfigFile}.dist";
    $installConfig = require $installConfigFile;
    $installOptions = isset($installConfig['install_options']) ? $installConfig['install_options'] : array();

    /* Install application */
    if ($installOptions) {
        $installCmd = sprintf('php -f %s --', escapeshellarg(realpath($testsBaseDir . '/../../shell/install.php')));
        foreach ($installOptions as $optionName => $optionValue) {
            $installCmd .= sprintf(' --%s %s', $optionName, escapeshellarg($optionValue));
        }
        echo $installCmd . "\n";
        passthru($installCmd, $exitCode);
        if ($exitCode) {
            exit($exitCode);
        }
    }
}

/* Unset declared global variables to release PHPUnit from maintaining their values between tests */
unset($bootstrap, $installCmd, $installConfigFile, $installConfig, $installExitCode);
