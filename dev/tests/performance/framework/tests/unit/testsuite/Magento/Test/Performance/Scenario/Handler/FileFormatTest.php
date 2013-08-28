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

class Magento_Test_Performance_Scenario_Handler_FileFormatTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Performance_Scenario_Handler_FileFormat
     */
    protected $_object;

    /**
     * @var Magento_TestFramework_Performance_Scenario_HandlerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_handler;

    /**
     * @var Magento_TestFramework_Performance_Scenario
     */
    protected $_scenario;

    protected function setUp()
    {
        $this->_handler = $this->getMockForAbstractClass('Magento_TestFramework_Performance_Scenario_HandlerInterface');
        $this->_object = new Magento_TestFramework_Performance_Scenario_Handler_FileFormat();
        $this->_object->register('jmx', $this->_handler);
        $this->_scenario =
            new Magento_TestFramework_Performance_Scenario('Scenario', 'scenario.jmx', array(), array(), array());
    }

    protected function tearDown()
    {
        $this->_handler = null;
        $this->_object = null;
        $this->_scenario = null;
    }

    public function testRegisterGetHandler()
    {
        $this->assertNull($this->_object->getHandler('php'));
        $this->_object->register('php', $this->_handler);
        $this->assertSame($this->_handler, $this->_object->getHandler('php'));
    }

    public function testRunDelegation()
    {
        $reportFile = 'scenario.jtl';
        $this->_handler
            ->expects($this->once())
            ->method('run')
            ->with($this->_scenario, $reportFile)
        ;
        $this->_object->run($this->_scenario, $reportFile);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Unable to run scenario 'Scenario', format is not supported.
     */
    public function testRunUnsupportedFormat()
    {
        $scenario =
            new Magento_TestFramework_Performance_Scenario('Scenario', 'scenario.txt', array(), array(), array());
        $this->_object->run($scenario);
    }
}
