<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Performance_Scenario_FailureExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Performance_Scenario_FailureException
     */
    protected $_object;

    /**
     * @var Magento_Performance_Scenario_Arguments
     */
    protected $_scenarioArgs;

    protected function setUp()
    {
        $this->_scenarioArgs = new Magento_Performance_Scenario_Arguments(array());
        $this->_object = new Magento_Performance_Scenario_FailureException(
            'scenario.jmx', $this->_scenarioArgs, 'scenario has failed'
        );
    }

    protected function tearDown()
    {
        $this->_object = null;
    }

    public function testConstructor()
    {
        $this->assertEquals('scenario has failed', $this->_object->getMessage());
    }

    public function testGetScenarioFile()
    {
        $this->assertEquals('scenario.jmx', $this->_object->getScenarioFile());
    }

    public function testGetScenarioArguments()
    {
        $this->assertSame($this->_scenarioArgs, $this->_object->getScenarioArguments());
    }
}
