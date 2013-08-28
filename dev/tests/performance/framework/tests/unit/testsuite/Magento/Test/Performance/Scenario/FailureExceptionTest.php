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

class Magento_Test_Performance_Scenario_FailureExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Performance_Scenario_FailureException
     */
    protected $_object;

    /**
     * @var Magento_TestFramework_Performance_Scenario
     */
    protected $_scenario;

    protected function setUp()
    {
        $this->_scenario = new Magento_TestFramework_Performance_Scenario('Title', '', array(), array(), array());
        $this->_object =
            new Magento_TestFramework_Performance_Scenario_FailureException($this->_scenario, 'scenario has failed');
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_scenario = null;
    }

    public function testConstructor()
    {
        $this->assertEquals('scenario has failed', $this->_object->getMessage());
    }

    public function testGetScenario()
    {
        $this->assertSame($this->_scenario, $this->_object->getScenario());
    }
}
