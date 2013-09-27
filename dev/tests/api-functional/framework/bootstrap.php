<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
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

\Magento\Autoload\IncludePath::addIncludePath(array(
    "{$testsBaseDir}/framework",
    "{$testsBaseDir}/testsuite",
    "{$integrationTestsDir}/framework",
    "{$integrationTestsDir}/lib"
));

/* Bootstrap the application */
$invariantSettings = array(
    'TESTS_LOCAL_CONFIG_EXTRA_FILE' => '../integration/etc/integration-tests-config.xml',
);
$bootstrap = new \Magento\TestFramework\Bootstrap(
    new \Magento\TestFramework\Bootstrap\Settings($testsBaseDir, $invariantSettings + get_defined_constants()),
    new \Magento\TestFramework\Bootstrap\Environment(),
    new \Magento\TestFramework\Bootstrap\WebapiDocBlock("{$integrationTestsDir}/testsuite"),
    new \Magento\TestFramework\Bootstrap\Profiler(new \Magento\Profiler\Driver\Standard()),
    new \Magento\Shell(),
    $testsTmpDir
);
$bootstrap->runBootstrap();
Magento_TestFramework_Helper_Bootstrap::setInstance(new \Magento\TestFramework\Helper\Bootstrap($bootstrap));
Magento_TestFramework_Utility_Files::init(new \Magento\TestFramework\Utility\Files($magentoBaseDir));

/** Magento installation */
if (defined('TESTS_MAGENTO_INSTALLATION') && TESTS_MAGENTO_INSTALLATION === 'enabled') {
    $installCmd = sprintf(
        'php -f %s --',
        escapeshellarg(realpath($testsBaseDir . '/../../shell/install.php'))
    );
    if (defined('TESTS_CLEANUP') && TESTS_CLEANUP === 'enabled') {
        passthru("$installCmd --uninstall", $exitCode);
        if ($exitCode) {
            exit($exitCode);
        }
    }
    $installConfigFile = $testsBaseDir . '/config/install.php';
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
unset($bootstrap, $installCmd, $installConfigFile, $installConfig, $installExitCode);
