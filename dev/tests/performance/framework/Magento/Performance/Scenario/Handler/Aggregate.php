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
 * Handler aggregating multiple performance scenario handlers
 */
class Magento_Performance_Scenario_Handler_Aggregate implements Magento_Performance_Scenario_HandlerInterface
{
    /**
     * @var array
     */
    protected $_handlers = array();

    /**
     * Constructor
     *
     * @param array $handlers Instances of Magento_Performance_Scenario_HandlerInterface
     * @throws InvalidArgumentException
     */
    public function __construct(array $handlers)
    {
        if (empty($handlers)) {
            throw new InvalidArgumentException('At least one scenario handler must be defined.');
        }
        foreach ($handlers as $oneScenarioHandler) {
            if (!($oneScenarioHandler instanceof Magento_Performance_Scenario_HandlerInterface)) {
                throw new InvalidArgumentException(
                    'Scenario handler must implement "Magento_Performance_Scenario_HandlerInterface".'
                );
            }
        }
        $this->_handlers = $handlers;
    }

    /**
     * Run scenario and optionally write results to report file
     *
     * @param string $scenarioFile
     * @param Magento_Performance_Scenario_Arguments $scenarioArguments
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     * @return bool Whether handler was able to process scenario
     */
    public function run($scenarioFile, Magento_Performance_Scenario_Arguments $scenarioArguments, $reportFile = null)
    {
        foreach ($this->_handlers as $oneScenarioHandler) {
            /** @var $oneScenarioHandler Magento_Performance_Scenario_HandlerInterface */
            if ($oneScenarioHandler->run($scenarioFile, $scenarioArguments, $reportFile)) {
                /* Stop execution upon first handling */
                return true;
            }
        }
        return false;
    }
}
