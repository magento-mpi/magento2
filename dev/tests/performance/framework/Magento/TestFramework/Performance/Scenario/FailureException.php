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
class Magento_TestFramework_Performance_Scenario_FailureException extends \Magento\Exception
{
    /**
     * @var Magento_TestFramework_Performance_Scenario
     */
    protected $_scenario;

    /**
     * Constructor
     *
     * @param Magento_TestFramework_Performance_Scenario $scenario
     * @param string $message
     */
    public function __construct(Magento_TestFramework_Performance_Scenario $scenario, $message = '')
    {
        parent::__construct($message);
        $this->_scenario = $scenario;
    }

    /**
     * Retrieve scenario
     *
     * @return Magento_TestFramework_Performance_Scenario
     */
    public function getScenario()
    {
        return $this->_scenario;
    }
}
