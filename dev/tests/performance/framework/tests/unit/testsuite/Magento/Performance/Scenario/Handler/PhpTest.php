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

class Magento_Performance_Scenario_Handler_PhpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_Performance_Scenario_Handler_Php|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_scenarioFile;

    /**
     * @var Magento_Performance_Scenario
     */
    protected $_scenario;

    /**
     * @var string
     */
    protected $_reportFile;

    protected function setUp()
    {
        $this->_scenarioFile = realpath(__DIR__ . '/../../_files/scenario.php');
        $scenarioArgs = array(
            Magento_Performance_Scenario::ARG_USERS => 2,
            Magento_Performance_Scenario::ARG_LOOPS => 3,
            'custom' => 'custom_value',
        );
        $this->_scenario = new Magento_Performance_Scenario('Scenario', $this->_scenarioFile, $scenarioArgs, array(),
            array());

        $this->_reportFile = realpath(__DIR__ . '/../../_files/scenario.jtl');
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_object = new Magento_Performance_Scenario_Handler_Php($this->_shell, false);
    }

    protected function tearDown()
    {
        $this->_shell = null;
        $this->_object = null;
        $this->_scenario = null;
    }

    public function testValidateScenarioExecutable()
    {
        $object = new Magento_Performance_Scenario_Handler_Php($this->_shell);

        $this->_shell
            ->expects($this->at(0))
            ->method('execute')
            ->with('php --version')
        ;
        $object->run($this->_scenario);

        // validation must be performed only once
        $this->_shell
            ->expects($this->any())
            ->method('execute')
            ->with($this->logicalNot($this->equalTo('php --version')))
        ;
        $object->run($this->_scenario);
    }

    public function testRunNoReport()
    {
        $this->_shell
            ->expects($this->exactly(3))
            ->method('execute')
            ->with(
                'php -f %s -- --users %s --loops %s --custom %s',
                array($this->_scenarioFile, 2, 3, 'custom_value')
            )
        ;
        $this->_object->run($this->_scenario);
    }

    public function testRunReport()
    {
        $this->expectOutputRegex('/.+/'); // prevent displaying output
        $this->_object->run($this->_scenario, 'php://output');
        $expectedDom = new DOMDocument();
        $expectedDom->loadXML('
            <testResults version="1.2">
            <httpSample t="100" lt="0" ts="1349212263" s="true" lb="Scenario" rc="0" rm="" tn="1" dt="text"/>
            <httpSample t="150" lt="0" ts="1349212263" s="true" lb="Scenario" rc="0" rm="" tn="2" dt="text"/>
            <httpSample t="125" lt="0" ts="1349212263" s="true" lb="Scenario" rc="0" rm="" tn="3" dt="text"/>
            </testResults>
        ');
        $actualDom = new DOMDocument();
        $actualDom->loadXML($this->getActualOutput());
        $this->assertEqualXMLStructure($expectedDom->documentElement, $actualDom->documentElement, true);
    }

    /**
     * @expectedException Magento_Performance_Scenario_FailureException
     * @expectedExceptionMessage command failure message
     */
    public function testRunException()
    {
        $failure = new Magento_Exception(
            'Command returned non-zero exit code.', 0, new Exception('command failure message', 1)
        );
        $this->_shell
            ->expects($this->any())
            ->method('execute')
            ->will($this->throwException($failure))
        ;
        $this->_object->run($this->_scenario);
    }
}
