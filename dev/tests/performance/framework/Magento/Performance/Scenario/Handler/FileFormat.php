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
 * Handler delegates execution to one of registered scenario handlers depending on a scenario file extension
 */
class Magento_Performance_Scenario_Handler_FileFormat implements Magento_Performance_Scenario_HandlerInterface
{
    /**
     * @var array
     */
    protected $_handlers = array();

    /**
     * Register scenario handler to process scenario files with a certain extension
     *
     * @param string $fileExtension
     * @param Magento_Performance_Scenario_HandlerInterface $handlerInstance
     * @return Magento_Performance_Scenario_Handler_FileFormat
     */
    public function register($fileExtension, Magento_Performance_Scenario_HandlerInterface $handlerInstance)
    {
        $this->_handlers[$fileExtension] = $handlerInstance;
        return $this;
    }

    /**
     * Retrieve scenario handler for a certain file extension or NULL, if no handlers have been registered for it
     *
     * @param string $fileExtension
     * @return Magento_Performance_Scenario_HandlerInterface|null
     */
    public function getHandler($fileExtension)
    {
        return isset($this->_handlers[$fileExtension]) ? $this->_handlers[$fileExtension] : null;
    }

    /**
     * Run scenario and optionally write results to report file
     *
     * @param string $scenarioFile
     * @param Magento_Performance_Scenario_Arguments $scenarioArguments
     * @param string|null $reportFile Report file to write results to, NULL disables report creation
     * @throws Magento_Exception
     */
    public function run($scenarioFile, Magento_Performance_Scenario_Arguments $scenarioArguments, $reportFile = null)
    {
        $scenarioExtension = pathinfo($scenarioFile, PATHINFO_EXTENSION);
        /** @var $scenarioHandler Magento_Performance_Scenario_HandlerInterface */
        $scenarioHandler = $this->getHandler($scenarioExtension);
        if (!$scenarioHandler) {
            throw new Magento_Exception("Unable to run scenario '$scenarioFile', format is not supported.");
        }
        $scenarioHandler->run($scenarioFile, $scenarioArguments, $reportFile);
    }
}
