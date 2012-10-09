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

class Magento_Performance_TestsuiteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Performance_Testsuite
     */
    protected $_object;

    /**
     * @var Magento_Performance_Config
     */
    protected $_config;

    /**
     * @var Magento_Application|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_application;

    /**
     * @var Magento_Performance_Scenario_HandlerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_handler;

    /**
     * @var string
     */
    protected $_fixtureDir;

    /**
     * @var string
     */
    protected $_appBaseDir;

    protected function setUp()
    {
        $this->_fixtureDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $fixtureConfigData = include($this->_fixtureDir . DIRECTORY_SEPARATOR . 'config_data.php');
        $shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_config = new Magento_Performance_Config(
            $fixtureConfigData,
            $this->_fixtureDir,
            $this->_fixtureDir . '/app_base_dir'
        );
        $this->_application = $this->getMock(
            'Magento_Application', array('applyFixtures'), array($this->_config, $shell)
        );
        $this->_handler = $this->getMockForAbstractClass('Magento_Performance_Scenario_HandlerInterface');
        $this->_object = new Magento_Performance_Testsuite($this->_config, $this->_application, $this->_handler);
    }

    protected function tearDown()
    {
        $this->_config = null;
        $this->_application = null;
        $this->_handler = null;
        $this->_object = null;
    }

    /**
     * Setup expectation of a scenario warm up invocation
     *
     * @param string $scenarioName
     * @param integer $invocationIndex
     * @param PHPUnit_Framework_MockObject_Stub $returnStub
     */
    protected function _expectScenarioWarmUp(
        $scenarioName, $invocationIndex, PHPUnit_Framework_MockObject_Stub $returnStub = null
    ) {
        /** @var $invocationMocker PHPUnit_Framework_MockObject_Builder_InvocationMocker */
        $invocationMocker = $this->_handler->expects($this->at($invocationIndex));
        $invocationMocker
            ->method('run')
            ->with(
                $this->_fixtureDir . DIRECTORY_SEPARATOR . $scenarioName . '.jmx',
                $this->isInstanceOf('Magento_Performance_Scenario_Arguments'),
                $this->isNull()
            )
        ;
        if ($returnStub) {
            $invocationMocker->will($returnStub);
        }
    }

    /**
     * Setup expectation of a scenario invocation with report generation
     *
     * @param string $scenarioName
     * @param integer $invocationIndex
     * @param PHPUnit_Framework_MockObject_Stub $returnStub
     */
    protected function _expectScenarioRun(
        $scenarioName, $invocationIndex, PHPUnit_Framework_MockObject_Stub $returnStub = null
    ) {
        /** @var $invocationMocker PHPUnit_Framework_MockObject_Builder_InvocationMocker */
        $invocationMocker = $this->_handler->expects($this->at($invocationIndex));
        $invocationMocker
            ->method('run')
            ->with(
                $this->_fixtureDir . DIRECTORY_SEPARATOR . $scenarioName . '.jmx',
                $this->isInstanceOf('Magento_Performance_Scenario_Arguments'),
                $this->_fixtureDir . DIRECTORY_SEPARATOR . 'report' . DIRECTORY_SEPARATOR . $scenarioName . '.jtl'
            )
        ;
        if ($returnStub) {
            $invocationMocker->will($returnStub);
        }
    }

    public function testRun()
    {
        $this->_expectScenarioWarmUp('scenario_error', 0);
        $this->_expectScenarioRun('scenario_error', 1);

        /* Warm up is disabled for scenario */
        $this->_expectScenarioRun('scenario_failure', 2);

        $this->_expectScenarioWarmUp('scenario', 3);
        $this->_expectScenarioRun('scenario', 4);

        $this->_object->run();
    }

    public function testOnScenarioRun()
    {
        $this->_handler
            ->expects($this->any())
            ->method('run')
        ;
        $notifications = array();
        $this->_object->onScenarioRun(function ($scenarioFile) use (&$notifications) {
            $notifications[] = $scenarioFile;
        });
        $this->_object->run();
        $this->assertEquals(array(
            $this->_fixtureDir . DIRECTORY_SEPARATOR . 'scenario_error.jmx',
            $this->_fixtureDir . DIRECTORY_SEPARATOR . 'scenario_failure.jmx',
            $this->_fixtureDir . DIRECTORY_SEPARATOR . 'scenario.jmx'
        ), $notifications);
    }

    /**
     * @expectedException BadFunctionCallException
     */
    public function testOnScenarioRunException()
    {
        $this->_object->onScenarioRun('invalid_callback');
    }

    public function testOnScenarioFailure()
    {
        $scenarioArgs = new Magento_Performance_Scenario_Arguments(array());

        $scenarioOneFailure = $this->throwException(
            new Magento_Performance_Scenario_FailureException('scenario_error.jmx', $scenarioArgs)
        );
        $this->_expectScenarioWarmUp('scenario_error', 0, $scenarioOneFailure);
        $this->_expectScenarioRun('scenario_error', 1, $scenarioOneFailure);

        /* Warm up is disabled for scenario */
        $scenarioTwoFailure = $this->throwException(
            new Magento_Performance_Scenario_FailureException('scenario_failure.jmx', $scenarioArgs)
        );
        $this->_expectScenarioRun('scenario_failure', 2, $scenarioTwoFailure);

        $scenarioThreeFailure = $this->throwException(
            new Magento_Performance_Scenario_FailureException('scenario.jmx', $scenarioArgs)
        );
        $this->_expectScenarioWarmUp('scenario', 3);
        $this->_expectScenarioRun('scenario', 4, $scenarioThreeFailure);

        $notifications = array();
        $this->_object->onScenarioFailure(
            function (Magento_Performance_Scenario_FailureException $actualFailure) use (&$notifications) {
                $notifications[] = $actualFailure->getScenarioFile();
            }
        );
        $this->_object->run();
        $this->assertEquals(array('scenario_error.jmx', 'scenario_failure.jmx', 'scenario.jmx'), $notifications);
    }

    /**
     * @expectedException BadFunctionCallException
     */
    public function testOnScenarioFailureException()
    {
        $this->_object->onScenarioFailure(array($this, 'invalid_callback'));
    }
}
