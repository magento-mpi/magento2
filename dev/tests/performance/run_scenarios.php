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

try {
    /** @var $config Magento_Config */
    $config = require_once __DIR__ . '/framework/bootstrap.php';

    $adminOptions = $config->getAdminOptions();
    $scenario = new Magento_Scenario(new Magento_Shell(true), $config->getJMeterPath(), $config->getReportDir());
    $scenarioParamsGlobal = array(
        Magento_Scenario::PARAM_HOST => $config->getApplicationUrlHost(),
        Magento_Scenario::PARAM_PATH => $config->getApplicationUrlPath(),
        Magento_Scenario::PARAM_ADMIN_FRONTNAME => $adminOptions['frontname'],
        Magento_Scenario::PARAM_ADMIN_USERNAME => $adminOptions['username'],
        Magento_Scenario::PARAM_ADMIN_PASSWORD => $adminOptions['password'],
    );
    $scenarioTotalCount = count($config->getScenarios());
    $scenarioFailCount = 0;
    $scenarioNum = 1;
    foreach ($config->getScenarios() as $scenarioFile => $scenarioParams) {
        echo "Scenario $scenarioNum of $scenarioTotalCount: '$scenarioFile'" . PHP_EOL;
        $scenarioParams = array_merge($scenarioParams, $scenarioParamsGlobal);
        try {
            $scenario->run($scenarioFile, $scenarioParams);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $scenarioFailCount++;
        }
        echo PHP_EOL;
        $scenarioNum++;
    }
    if ($scenarioFailCount) {
        throw new Magento_Exception("Failed $scenarioFailCount of $scenarioTotalCount scenario(s)");
    }
    echo 'Successful' . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
