<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/../../static/framework/Magento/TestFramework/Utility/Classes.php';
require_once __DIR__ . '/../../static/framework/Magento/TestFramework/Utility/AggregateInvoker.php';

$testsBaseDir = dirname(__DIR__);
$testsTmpDir = "{$testsBaseDir}/tmp";
$magentoBaseDir = realpath("{$testsBaseDir}/../../../");

(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
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

try {
    /* Bootstrap the application */
    $settings = new \Magento\TestFramework\Bootstrap\Settings($testsBaseDir, get_defined_constants());

    if ($settings->get('TESTS_EXTRA_VERBOSE_LOG')) {
        $logWriter = new \Zend_Log_Writer_Stream('php://output');
        $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
        $shell = new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer, new \Zend_Log($logWriter));
    } else {
        $shell = new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer);
    }

    $application = \Magento\TestFramework\Application::getInstance(
        $settings->getAsConfigFile('TESTS_INSTALL_CONFIG_FILE'),
        $settings->get('TESTS_GLOBAL_CONFIG_DIR'),
        $settings->get('TESTS_MAGENTO_MODE'),
        $testsTmpDir,
        $shell
    );

    $bootstrap = new \Magento\TestFramework\Bootstrap(
        $settings,
        new \Magento\TestFramework\Bootstrap\Environment(),
        new \Magento\TestFramework\Bootstrap\DocBlock("{$testsBaseDir}/testsuite"),
        new \Magento\TestFramework\Bootstrap\Profiler(new \Magento\Framework\Profiler\Driver\Standard()),
        $shell,
        $application,
        new \Magento\TestFramework\Bootstrap\MemoryFactory($shell)
    );
    $bootstrap->runBootstrap();
    if ($settings->getAsBoolean('TESTS_CLEANUP')) {
        $application->cleanup();
    }
    if (!$application->isInstalled()) {
        $application->install();
    }
    $application->initialize();

    \Magento\TestFramework\Helper\Bootstrap::setInstance(new \Magento\TestFramework\Helper\Bootstrap($bootstrap));

    \Magento\TestFramework\Utility\Files::setInstance(new Magento\TestFramework\Utility\Files($magentoBaseDir));

    /* Unset declared global variables to release the PHPUnit from maintaining their values between tests */
    unset($testsBaseDir, $testsTmpDir, $magentoBaseDir, $logWriter, $settings, $shell, $application, $bootstrap);
} catch (\Exception $e) {
    echo $e . PHP_EOL;
    exit(1);
}
