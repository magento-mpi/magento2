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
$totalStartTime = microtime(true);

$shell = new Zend_Console_Getopt(
    array(
        'profile-s' => 'Profile configuration file',
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
$config->loadLabels(__DIR__ . '/framework/labels.xml');

$labels = $config->getLabels();

echo 'Generating profile with following params:' . PHP_EOL;
foreach ($labels as $configKey => $label) {
    echo ' |- ' . $label . ': ' . $config->getValue($configKey) . PHP_EOL;
}

$files = \Magento\ToolkitFramework\FixtureSet::getInstance()->getFixtures();

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

$shell = new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer(), $logger);

$application = new \Magento\ToolkitFramework\Application($applicationBaseDir, $shell);
$application->bootstrap();

foreach ($files as $fixture) {
    echo $fixture['action'] . '... ';
    $startTime = microtime(true);
    $application->applyFixture(__DIR__ . '/fixtures/' . $fixture['file']);
    $endTime = microtime(true);
    $resultTime = $endTime - $startTime;
    echo ' done in ' . gmdate('H:i:s', $resultTime) . PHP_EOL;
}

$application->reindex();
$totalEndTime = microtime(true);
$totalResultTime = $totalEndTime - $totalStartTime;

echo 'Total execution time: ' . gmdate('H:i:s', $totalResultTime) . PHP_EOL;
