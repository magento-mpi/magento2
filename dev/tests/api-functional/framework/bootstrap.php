<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\Framework\Autoload\AutoloaderRegistry;

require_once __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/../lib/OAuth/bootstrap.php';
require_once __DIR__ . '/autoload.php';

$testsBaseDir = dirname(__DIR__);
$integrationTestsDir = realpath("{$testsBaseDir}/../integration");

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

/* Bootstrap the application */
$settings = new \Magento\TestFramework\Bootstrap\Settings($testsBaseDir, get_defined_constants());
$shell = new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer(), $logger);

$application = \Magento\TestFramework\WebApiApplication::getInstance(
    $settings->getAsConfigFile('TESTS_INSTALL_CONFIG_FILE'),
    BP . '/app/etc/',
    glob(BP . '/app/etc/*/module.xml'),
    $settings->get('TESTS_MAGENTO_MODE'),
    $shell,
    AutoloaderRegistry::getAutoloader()
);
if (defined('TESTS_MAGENTO_INSTALLATION') && TESTS_MAGENTO_INSTALLATION === 'enabled') {
    if (defined('TESTS_CLEANUP') && TESTS_CLEANUP === 'enabled') {
        $application->cleanup();
    }
    $application->install();
}

$bootstrap = new \Magento\TestFramework\Bootstrap(
    $settings,
    new \Magento\TestFramework\Bootstrap\Environment(),
    new \Magento\TestFramework\Bootstrap\WebapiDocBlock("{$integrationTestsDir}/testsuite"),
    new \Magento\TestFramework\Bootstrap\Profiler(new \Magento\Framework\Profiler\Driver\Standard()),
    $shell,
    $application,
    new \Magento\TestFramework\Bootstrap\MemoryFactory($shell)
);
$bootstrap->runBootstrap();
$application->initialize();

\Magento\TestFramework\Helper\Bootstrap::setInstance(new \Magento\TestFramework\Helper\Bootstrap($bootstrap));
\Magento\Framework\Test\Utility\Files::setInstance(new \Magento\Framework\Test\Utility\Files(BP));
unset($bootstrap, $application, $settings, $shell);
