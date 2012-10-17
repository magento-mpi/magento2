<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for handling performance testing scenarios
 */
interface Magento_Performance_Scenario_HandlerInterface
{
    /**
     * Run scenario and optionally write results to report file
     *
     * @param string $scenarioFile
     * @param Magento_Performance_Scenario_Arguments $scenarioArguments
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     * @return bool Whether handler was able to process scenario
     */
    public function run($scenarioFile, Magento_Performance_Scenario_Arguments $scenarioArguments, $reportFile = null);
}
