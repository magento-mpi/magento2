<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once __DIR__ . '/../../../../app/bootstrap.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
spl_autoload_register([$includePath, 'load']);
require_once __DIR__ . '/../../static/framework/Magento/TestFramework/Utility/Classes.php';
require_once __DIR__ . '/../../static/framework/Magento/TestFramework/Utility/AggregateInvoker.php';

$testsBaseDir = dirname(__DIR__);
$testsTmpDir = "{$testsBaseDir}/tmp";
$magentoBaseDir = realpath("{$testsBaseDir}/../../../");

$includePath->addIncludePath(
    array("{$testsBaseDir}/framework", "{$testsBaseDir}/testsuite")
);

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }

    $filePath = str_replace('\\', '/', $className);
    $filePath = BP . '/dev/tools/' . $filePath . '.php';

    if (file_exists($filePath)) {
        include_once $filePath;
    } else {
        return false;
    }
}

spl_autoload_register('tool_autoloader');

/* Bootstrap the application */
$invariantSettings = array('TESTS_LOCAL_CONFIG_EXTRA_FILE' => 'etc/integration-tests-config.xml');
$bootstrap = new \Magento\TestFramework\Bootstrap(
    new \Magento\TestFramework\Bootstrap\Settings($testsBaseDir, $invariantSettings + get_defined_constants()),
    new \Magento\TestFramework\Bootstrap\Environment(),
    new \Magento\TestFramework\Bootstrap\DocBlock("{$testsBaseDir}/testsuite"),
    new \Magento\TestFramework\Bootstrap\Profiler(new \Magento\Framework\Profiler\Driver\Standard()),
    new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer()),
    $testsTmpDir
);
$bootstrap->runBootstrap();

\Magento\TestFramework\Helper\Bootstrap::setInstance(new \Magento\TestFramework\Helper\Bootstrap($bootstrap));

Magento\TestFramework\Utility\Files::setInstance(new Magento\TestFramework\Utility\Files($magentoBaseDir));

/* Unset declared global variables to release the PHPUnit from maintaining their values between tests */
unset($bootstrap);
