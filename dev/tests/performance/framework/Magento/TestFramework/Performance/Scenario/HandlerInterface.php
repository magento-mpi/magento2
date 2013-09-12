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
interface Magento_TestFramework_Performance_Scenario_HandlerInterface
{
    /**
     * Run scenario and optionally write results to report file
     *
     * @param Magento_TestFramework_Performance_Scenario $scenario
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     */
    public function run(Magento_TestFramework_Performance_Scenario $scenario, $reportFile = null);
}
