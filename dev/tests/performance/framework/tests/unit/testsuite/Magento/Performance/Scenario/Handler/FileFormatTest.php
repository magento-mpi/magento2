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

class Magento_Performance_Scenario_Handler_FileFormatTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Performance_Scenario_Handler_FileFormat
     */
    protected $_object;

    /**
     * @var Magento_Performance_Scenario_HandlerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_handler;

    /**
     * @var Magento_Performance_Scenario_Arguments
     */
    protected $_scenarioArgs;

    protected function setUp()
    {
        $this->_handler = $this->getMockForAbstractClass('Magento_Performance_Scenario_HandlerInterface');
        $this->_object = new Magento_Performance_Scenario_Handler_FileFormat();
        $this->_object->register('jmx', $this->_handler);
        $this->_scenarioArgs = new Magento_Performance_Scenario_Arguments(array());
    }

    protected function tearDown()
    {
        $this->_handler = null;
        $this->_object = null;
        $this->_scenarioArgs = null;
    }

    public function testRegisterGetHandler()
    {
        $this->assertNull($this->_object->getHandler('php'));
        $this->_object->register('php', $this->_handler);
        $this->assertSame($this->_handler, $this->_object->getHandler('php'));
    }

    public function testRunDelegation()
    {
        $scenarioFile = 'scenario.jmx';
        $reportFile = 'scenario.jtl';
        $this->_handler
            ->expects($this->once())
            ->method('run')
            ->with($scenarioFile, $this->_scenarioArgs, $reportFile)
        ;
        $this->_object->run($scenarioFile, $this->_scenarioArgs, $reportFile);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage Unable to run scenario 'scenario.txt', format is not supported.
     */
    public function testRunUnsupportedFormat()
    {
        $this->_object->run('scenario.txt', $this->_scenarioArgs);
    }
}
