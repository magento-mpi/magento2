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

class Benchmark_ScenarioTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Benchmark_Scenario|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_scenarioFile;

    /**
     * @var string
     */
    protected $_reportFile;

    /**
     * @var array
     */
    protected $_scenarioParams = array(
        Benchmark_Scenario::PARAM_HOST  => '127.0.0.1',
        Benchmark_Scenario::PARAM_PATH  => '/',
        Benchmark_Scenario::PARAM_USERS => 2,
    );

    protected function setUp()
    {
        $this->_scenarioFile = __DIR__ . '/_files/scenario.jmx';
        $this->_reportFile = __DIR__ . '/_files/scenario.jtl';
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_object = $this->getMock(
            'Benchmark_Scenario',
            array('_loadReportXml'),
            array($this->_scenarioFile, $this->_scenarioParams, __DIR__ . '/_files', 'JMeter.jar', $this->_shell)
        );
    }

    protected function tearDown()
    {
        unset($this->_shell);
        unset($this->_object);
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @expectedException Magento_Exception
     */
    public function testConstructorException($scenarioFile, array $scenarioParams)
    {
        new Benchmark_Scenario($scenarioFile, $scenarioParams, __DIR__ . '/_files', 'JMeter.jar', $this->_shell);
    }

    public function constructorExceptionDataProvider()
    {
        return array(
            'non-existing scenario' => array(
                'non_existing_scenario.jmx',
                $this->_scenarioParams,
            ),
            'no "host" param' => array(
                __DIR__ . '/_files/scenario.jmx',
                array(Benchmark_Scenario::PARAM_PATH => '/'),
            ),
            'no "path" param' => array(
                __DIR__ . '/_files/scenario.jmx',
                array(Benchmark_Scenario::PARAM_HOST => '127.0.0.1'),
            ),
        );
    }

    public function testRun()
    {
        $this->_object
            ->expects($this->once())
            ->method('_loadReportXml')
            ->will($this->returnValue(simplexml_load_file(__DIR__ . '/_files/scenario.jtl')))
        ;
        $this->_shell
            ->expects($this->at(0))
            ->method('execute')
            ->with('java -jar %s --version', array('JMeter.jar'))
        ;
        $this->_shell
            ->expects($this->at(1))
            ->method('execute')
            ->with(
                'java -jar %s -n -t %s -l %s %s %s %s %s',
                array(
                    'JMeter.jar', $this->_scenarioFile, $this->_reportFile,
                    '-Jhost=127.0.0.1', '-Jpath=/', '-Jusers=2', '-Jloops=1'
                )
            )
        ;
        $this->_object->run();
    }

    /**
     * @dataProvider runExceptionDataProvider
     * @param $scenarioReportFile
     * @param $expectedExceptionMsg
     */
    public function testRunException($scenarioReportFile, $expectedExceptionMsg)
    {
        $this->setExpectedException('Magento_Exception', $expectedExceptionMsg);
        $this->_object
            ->expects($this->once())
            ->method('_loadReportXml')
            ->will($this->returnValue(simplexml_load_file($scenarioReportFile)))
        ;
        $this->_object->run();
    }

    public function runExceptionDataProvider()
    {
        return array(
            'assertion failure' => array(__DIR__ . '/_files/scenario_failure.jtl', 'fixture failure message'),
            'scenario error'    => array(__DIR__ . '/_files/scenario_error.jtl',   'fixture error message'),
        );
    }

    public function testRunDry()
    {
        $this->_shell
            ->expects($this->at(0))
            ->method('execute')
            ->with("java -jar %s --version", array('JMeter.jar'))
        ;
        $this->_shell
            ->expects($this->at(1))
            ->method('execute')
            ->with(
                'java -jar %s -n -t %s %s %s %s %s',
                array('JMeter.jar', $this->_scenarioFile, '-Jhost=127.0.0.1', '-Jpath=/', '-Jusers=1', '-Jloops=3')
            )
        ;
        $this->_object->runDry(3);
    }
}
