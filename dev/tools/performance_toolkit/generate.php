<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_toolkit
 * @copyright   {copyright}
 * @license     {license_link}
 */

$applicationBaseDir = require_once __DIR__ . '/framework/bootstrap.php';

$shell = new Zend_Console_Getopt(
    array(
        'profile-s'                         => 'Profile configuration file',
    )
);

\Magento\ToolkitFramework\Helper\Cli::setOpt($shell);

$args = $shell->getOptions();
if (empty($args)) {
    echo $shell->getUsageMessage();
    exit(0);
}

$config = \Magento\ToolkitFramework\Config::getInstance();
$config->loadConfig(\Magento\ToolkitFramework\Helper\Cli::getOption('profile'));
$files = \Magento\ToolkitFramework\FixtureSet::getInstance()->getFixtures();

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

$shell = new \Magento\Shell(new \Magento\OSInfo(), $logger);

$application = new \Magento\ToolkitFramework\Application($applicationBaseDir, $shell);
$application->bootstrap();

foreach ($files as $fixture) {
    echo 'Applying fixture ' . $fixture . PHP_EOL;
    $application->applyFixture(__DIR__ . '/fixtures/' . $fixture);
}

$application->reindex();
