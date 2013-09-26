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

/** @var $config Magento_TestFramework_Performance_Config */
$config = require_once __DIR__ . '/framework/bootstrap.php';

$logWriter = new Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new Zend_Log($logWriter);

$shell = new Magento_Shell($logger);
$scenarioHandler = new Magento_TestFramework_Performance_Scenario_Handler_FileFormat();
$scenarioHandler
    ->register('jmx', new Magento_TestFramework_Performance_Scenario_Handler_Jmeter($shell))
    ->register('php', new Magento_TestFramework_Performance_Scenario_Handler_Php($shell))
;

$testsuite =
    new Magento_TestFramework_Performance_Testsuite($config, new Magento_TestFramework_Application($config, $shell),
    $scenarioHandler);

$scenarioTotalCount = count($config->getScenarios());
$scenarioCount = 1;
$scenarioFailCount = 0;
$testsuite->onScenarioRun(
    function (
        Magento_TestFramework_Performance_Scenario $scenario
    ) use ($logger, &$scenarioCount, $scenarioTotalCount) {
        $logger->log("Scenario $scenarioCount of $scenarioTotalCount: '{$scenario->getTitle()}'", Zend_Log::INFO);
        $scenarioCount++;
    }
);
$testsuite->onScenarioFailure(
    function (
        Magento_TestFramework_Performance_Scenario_FailureException $scenarioFailure
    ) use ($logger, &$scenarioFailCount) {
        $scenario = $scenarioFailure->getScenario();
        $logger->log("Scenario '{$scenario->getTitle()}' has failed!", Zend_Log::ERR);
        $logger->log($scenarioFailure->getMessage(), Zend_Log::ERR);
        $scenarioFailCount++;
    }
);

$testsuite->run();

if ($scenarioFailCount) {
    $logger->log("Failed $scenarioFailCount of $scenarioTotalCount scenario(s)", Zend_Log::INFO);
    exit(1);
} else {
    $logger->log('Successful', Zend_Log::INFO);
}
