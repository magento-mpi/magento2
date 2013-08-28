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

class Magento_Test_Performance_Scenario_Handler_JmeterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_TestFramework_Performance_Scenario_Handler_Jmeter|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_scenarioFile;

    /**
     * @var Magento_TestFramework_Performance_Scenario
     */
    protected $_scenario;

    /**
     * @var string
     */
    protected $_reportFile;

    protected function setUp()
    {
        $this->_scenarioFile = realpath(__DIR__ . '/../../_files/scenario.jmx');
        $scenarioArgs = array(
            Magento_TestFramework_Performance_Scenario::ARG_HOST  => '127.0.0.1',
            Magento_TestFramework_Performance_Scenario::ARG_PATH  => '/',
            Magento_TestFramework_Performance_Scenario::ARG_USERS => 2,
            Magento_TestFramework_Performance_Scenario::ARG_LOOPS => 3,
        );
        $this->_scenario = new Magento_TestFramework_Performance_Scenario('Scenario', $this->_scenarioFile,
            $scenarioArgs, array(), array());

        $this->_reportFile = realpath(__DIR__ . '/../../_files') . DIRECTORY_SEPARATOR . 'scenario.jtl';
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_object = new Magento_TestFramework_Performance_Scenario_Handler_Jmeter($this->_shell, false);
    }

    protected function tearDown()
    {
        $this->_shell = null;
        $this->_object = null;
        $this->_scenario = null;
    }

    public function testValidateScenarioExecutable()
    {
        $object = new Magento_TestFramework_Performance_Scenario_Handler_Jmeter($this->_shell, true);

        $this->_shell
            ->expects($this->at(0))
            ->method('execute')
            ->with('jmeter --version')
        ;
        $object->run($this->_scenario);

        // validation must be performed only once
        $this->_shell
            ->expects($this->any())
            ->method('execute')
            ->with($this->logicalNot($this->equalTo('jmeter --version')))
        ;
        $object->run($this->_scenario);
    }

    public function testRunNoReport()
    {
        $this->_shell
            ->expects($this->once())
            ->method('execute')
            ->with(
                'jmeter -n -t %s %s %s %s %s',
                array($this->_scenarioFile, '-Jhost=127.0.0.1', '-Jpath=/', '-Jusers=2', '-Jloops=3')
            )
        ;
        $this->_object->run($this->_scenario);
    }

    public function testRunReport()
    {
        $this->_shell
            ->expects($this->once())
            ->method('execute')
            ->with(
                'jmeter -n -t %s -l %s %s %s %s %s',
                array(
                    $this->_scenarioFile, $this->_reportFile, '-Jhost=127.0.0.1', '-Jpath=/', '-Jusers=2', '-Jloops=3',
                )
            )
        ;
        $this->_object->run($this->_scenario, $this->_reportFile);
    }

    /**
     * @param string $scenarioFile
     * @param string $reportFile
     * @param string $expectedException
     * @param string $expectedExceptionMsg
     * @dataProvider runExceptionDataProvider
     */
    public function testRunException($scenarioFile, $reportFile, $expectedException, $expectedExceptionMsg = '')
    {
        $this->setExpectedException($expectedException, $expectedExceptionMsg);
        $scenario =
            new Magento_TestFramework_Performance_Scenario('Scenario', $scenarioFile, array(), array(), array());
        $this->_object->run($scenario, $reportFile);
    }

    public function runExceptionDataProvider()
    {
        $fixtureDir = realpath(__DIR__ . '/../../_files');
        return array(
            'no report created' => array(
                "$fixtureDir/scenario_without_report.jmx",
                "$fixtureDir/scenario_without_report.jtl",
                'Magento_Exception',
                "Report file '$fixtureDir/scenario_without_report.jtl' for 'Scenario' has not been created.",
            ),
            'scenario failure in report' => array(
                "$fixtureDir/scenario_failure.jmx",
                "$fixtureDir/scenario_failure.jtl",
                'Magento_TestFramework_Performance_Scenario_FailureException',
                'fixture failure message',
            ),
            'scenario error in report' => array(
                "$fixtureDir/scenario_error.jmx",
                "$fixtureDir/scenario_error.jtl",
                'Magento_TestFramework_Performance_Scenario_FailureException',
                'fixture error message',
            ),
        );
    }
}
