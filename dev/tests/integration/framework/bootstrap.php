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
require_once __DIR__ . '/../../static/testsuite/Utility/Classes.php';

$testsBaseDir = dirname(__DIR__);
$testsTmpDir = "$testsBaseDir/tmp";
$magentoBaseDir = realpath("$testsBaseDir/../../../");

Magento_Autoload_IncludePath::addIncludePath(array(
    "$testsBaseDir/framework",
    "$testsBaseDir/testsuite",
    "$magentoBaseDir/dev/lib",
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

Utility_Files::init(new Utility_Files($magentoBaseDir));

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', DS, str_replace('Magento\\Tools\\', '', $className));
    $filePath = BP . DS . 'dev' . DS . 'tools' . DS . $filePath . '.php';

    if (file_exists($filePath)) {
        include($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');

/* Unset declared global variables to release the PHPUnit from maintaining their values between tests */
unset($bootstrap);
