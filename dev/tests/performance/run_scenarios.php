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

/** @var $bootstrap Magento_Performance_Config */
$config = require_once __DIR__ . '/framework/bootstrap.php';

$shell = new Magento_Shell(true);
$scenarioHandler = new Magento_Performance_Scenario_Handler_Statistics(
    new Magento_Performance_Scenario_Handler_Aggregate(array(
        new Magento_Performance_Scenario_Handler_Jmeter($shell),
        new Magento_Performance_Scenario_Handler_Php($shell),
    ))
);

$scenarioTotalCount = count($config->getScenarios());
$scenarioCount = 1;
$scenarioHandler->onScenarioFirstRun(function ($scenarioFile) use (&$scenarioCount, $scenarioTotalCount) {
    echo "Scenario $scenarioCount of $scenarioTotalCount: '$scenarioFile'" . PHP_EOL;
    $scenarioCount++;
});
$scenarioHandler->onScenarioFailure(function ($scenarioFile, Magento_Performance_Scenario_FailureException $failure) {
    echo "Scenario '$scenarioFile' has failed!" . PHP_EOL . $failure->getMessage() . PHP_EOL . PHP_EOL;
});

$testsuite = new Magento_Performance_Testsuite($config, new Magento_Application($config, $shell), $scenarioHandler);
$testsuite->run();

$scenarioFailures = $scenarioHandler->getFailures();
if ($scenarioFailures) {
    $scenarioFailCount = count($scenarioFailures);
    echo "Failed $scenarioFailCount of $scenarioTotalCount scenario(s)" . PHP_EOL;
    exit(1);
} else {
    echo 'Successful' . PHP_EOL;
}
