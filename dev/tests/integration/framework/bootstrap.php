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

$testsBaseDir = dirname(__DIR__);
$testsTmpDir = "$testsBaseDir/tmp";
$magentoBaseDir = realpath("$testsBaseDir/../../../");

Magento_Autoload_IncludePath::addIncludePath(array(
    "$testsBaseDir/framework",
    "$testsBaseDir/testsuite",
));

/* Bootstrap the application */
$invariantSettings = array(
    'TESTS_LOCAL_CONFIG_EXTRA_FILE' => 'etc/integration-tests-config.xml',
);
$bootstrap = new Magento_Test_Bootstrap(
    new Magento_Test_Bootstrap_Settings($testsBaseDir, $invariantSettings + get_defined_constants()),
    new Magento_Test_Bootstrap_Environment(),
    new Magento_Test_Bootstrap_DocBlock("$testsBaseDir/testsuite"),
    new Magento_Test_Bootstrap_Profiler(new Magento_Profiler_Driver_Standard()),
    new Magento_Shell(),
    $testsTmpDir
);
$bootstrap->runBootstrap();

Magento_Test_Helper_Bootstrap::setInstance(new Magento_Test_Helper_Bootstrap($bootstrap));

Magento_TestFramework_Utility_Files::init(new Magento_TestFramework_Utility_Files($magentoBaseDir));

/* Unset declared global variables to release the PHPUnit from maintaining their values between tests */
unset($bootstrap);
