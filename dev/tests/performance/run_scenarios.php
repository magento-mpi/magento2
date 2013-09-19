<?php
/**
 * JMeter scenarios execution script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $config \Magento\TestFramework\Performance\Config */
$config = require_once __DIR__ . '/framework/bootstrap.php';

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

$shell = new \Magento\Shell($logger);
$scenarioHandler = new \Magento\TestFramework\Performance\Scenario\Handler\FileFormat();
$scenarioHandler
    ->register('jmx', new \Magento\TestFramework\Performance\Scenario\Handler\Jmeter($shell))
    ->register('php', new \Magento\TestFramework\Performance\Scenario\Handler\Php($shell))
;

$testsuite =
    new \Magento\TestFramework\Performance\Testsuite($config, new \Magento\TestFramework\Application($config, $shell),
    $scenarioHandler);

$scenarioTotalCount = count($config->getScenarios());
$scenarioCount = 1;
$scenarioFailCount = 0;
$testsuite->onScenarioRun(
    function (\Magento\TestFramework\Performance\Scenario $scenario)
        use ($logger, &$scenarioCount, $scenarioTotalCount) {
        $logger->log("Scenario $scenarioCount of $scenarioTotalCount: '{$scenario->getTitle()}'", \Zend_Log::INFO);
        $scenarioCount++;
    }
);
$testsuite->onScenarioFailure(
    function (\Magento\TestFramework\Performance\Scenario\FailureException $scenarioFailure)
        use ($logger, &$scenarioFailCount) {
        $scenario = $scenarioFailure->getScenario();
        $logger->log("Scenario '{$scenario->getTitle()}' has failed!", \Zend_Log::ERR);
        $logger->log($scenarioFailure->getMessage(), \Zend_Log::ERR);
        $scenarioFailCount++;
    }
);

$testsuite->run();

if ($scenarioFailCount) {
    $logger->log("Failed $scenarioFailCount of $scenarioTotalCount scenario(s)", \Zend_Log::INFO);
    exit(1);
} else {
    $logger->log('Successful', \Zend_Log::INFO);
}
