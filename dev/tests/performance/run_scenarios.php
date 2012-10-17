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

/** @var $config Magento_Performance_Config */
$config = require_once __DIR__ . '/framework/bootstrap.php';

$shell = new Magento_Shell(true);
$scenarioHandler = new Magento_Performance_Scenario_Handler_FileFormat();
$scenarioHandler
    ->register('jmx', new Magento_Performance_Scenario_Handler_Jmeter($shell))
    ->register('php', new Magento_Performance_Scenario_Handler_Php($shell))
;

$testsuite = new Magento_Performance_Testsuite($config, new Magento_Application($config, $shell), $scenarioHandler);

$scenarioTotalCount = count($config->getScenarios());
$scenarioCount = 1;
$scenarioFailCount = 0;
$testsuite->onScenarioRun(function (Magento_Performance_Scenario $scenario) use (&$scenarioCount, $scenarioTotalCount) {
    echo "Scenario $scenarioCount of $scenarioTotalCount: '{$scenario->getTitle()}'" . PHP_EOL;
    $scenarioCount++;
});
$testsuite->onScenarioFailure(
    function (Magento_Performance_Scenario_FailureException $scenarioFailure) use (&$scenarioFailCount) {
        $scenario = $scenarioFailure->getScenario();
        echo "Scenario '{$scenario->getTitle()}' has failed!" . PHP_EOL
            . $scenarioFailure->getMessage() . PHP_EOL . PHP_EOL;
        $scenarioFailCount++;
    }
);

$testsuite->run();

if ($scenarioFailCount) {
    echo "Failed $scenarioFailCount of $scenarioTotalCount scenario(s)" . PHP_EOL;
    exit(1);
} else {
    echo 'Successful' . PHP_EOL;
}
