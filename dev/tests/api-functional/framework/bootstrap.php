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
$invariantSettings = array('TESTS_LOCAL_CONFIG_EXTRA_FILE' => '../integration/etc/integration-tests-config.xml');
$bootstrap = new \Magento\TestFramework\ApiBootstrap(
    new \Magento\TestFramework\Bootstrap\Settings($testsBaseDir, $invariantSettings + get_defined_constants()),
    new \Magento\TestFramework\Bootstrap\Environment(),
    new \Magento\TestFramework\Bootstrap\WebapiDocBlock("{$integrationTestsDir}/testsuite"),
    new \Magento\TestFramework\Bootstrap\Profiler(new \Magento\Framework\Profiler\Driver\Standard()),
    new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer()),
    $testsTmpDir,
    $magentoBaseDir
);
$bootstrap->runBootstrap();
\Magento\TestFramework\Helper\Bootstrap::setInstance(new \Magento\TestFramework\Helper\Bootstrap($bootstrap));
\Magento\TestFramework\Utility\Files::setInstance(new \Magento\TestFramework\Utility\Files($magentoBaseDir));

unset($bootstrap, $installCmd, $installConfigFile, $installConfig, $installExitCode);
