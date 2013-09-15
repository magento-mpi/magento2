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

\Magento\Autoload\IncludePath::addIncludePath(array(
    "$testsBaseDir/framework",
    "$testsBaseDir/testsuite",
));

/* Bootstrap the application */
$invariantSettings = array(
    'TESTS_LOCAL_CONFIG_EXTRA_FILE' => 'etc/integration-tests-config.xml',
);
$bootstrap = new Magento_TestFramework_Bootstrap(
    new Magento_TestFramework_Bootstrap_Settings($testsBaseDir, $invariantSettings + get_defined_constants()),
    new Magento_TestFramework_Bootstrap_Environment(),
    new Magento_TestFramework_Bootstrap_DocBlock("$testsBaseDir/testsuite"),
    new Magento_TestFramework_Bootstrap_Profiler(new \Magento\Profiler\Driver\Standard()),
    new \Magento\Shell(),
    $testsTmpDir
);
$bootstrap->runBootstrap();

Magento_TestFramework_Helper_Bootstrap::setInstance(new Magento_TestFramework_Helper_Bootstrap($bootstrap));

Magento_TestFramework_Utility_Files::init(new Magento_TestFramework_Utility_Files($magentoBaseDir));

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', DS, $className);
    $filePath = BP . DS . 'dev' . DS . 'tools' . DS . $filePath . '.php';

    if (file_exists($filePath)) {
        include_once($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');

/* Unset declared global variables to release the PHPUnit from maintaining their values between tests */
unset($bootstrap);
