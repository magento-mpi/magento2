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
 * Exceptional situation of a performance testing scenario failure
 */
class Magento_Performance_Scenario_FailureException extends Magento_Exception
{
    /**
     * @var string
     */
    protected $_scenarioFile;

    /**
     * @var Magento_Performance_Scenario_Arguments
     */
    protected $_scenarioArguments;

    /**
     * Constructor
     *
     * @param string $scenarioFile
     * @param Magento_Performance_Scenario_Arguments $scenarioArguments
     * @param string $message
     */
    public function __construct($scenarioFile, Magento_Performance_Scenario_Arguments $scenarioArguments, $message = '')
    {
        parent::__construct($message);
        $this->_scenarioFile = $scenarioFile;
        $this->_scenarioArguments = $scenarioArguments;
    }

    /**
     * Retrieve scenario file
     *
     * @return string
     */
    public function getScenarioFile()
    {
        return $this->_scenarioFile;
    }

    /**
     * Retrieve scenario arguments
     *
     * @return Magento_Performance_Scenario_Arguments
     */
    public function getScenarioArguments()
    {
        return $this->_scenarioArguments;
    }
}
