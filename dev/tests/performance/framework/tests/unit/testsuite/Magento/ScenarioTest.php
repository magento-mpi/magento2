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

class Magento_ScenarioTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_Scenario|PHPUnit_Framework_MockObject_MockObject
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
        Magento_Scenario::PARAM_HOST  => '127.0.0.1',
        Magento_Scenario::PARAM_PATH  => '/',
        Magento_Scenario::PARAM_USERS => 2,
    );

    protected function setUp()
    {
        $this->_scenarioFile = realpath(__DIR__ . '/_files/scenario.jmx');
        $reportDir = realpath(__DIR__ . '/_files');
        $this->_reportFile = $reportDir . DIRECTORY_SEPARATOR . 'scenario.jtl';
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));
        $this->_object = new Magento_Scenario($this->_shell, 'JMeter.jar', $reportDir);
    }

    protected function tearDown()
    {
        unset($this->_shell);
        unset($this->_object);
    }

    public function testRun()
    {
        $this->_shell
            ->expects($this->at(0))
            ->method('execute')
            ->with(
            'java -jar %s -n -t %s %s %s %s %s',
            array('JMeter.jar', $this->_scenarioFile, '-Jhost=127.0.0.1', '-Jpath=/', '-Jusers=1', '-Jloops=2')
        )
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
        $this->_object->run($this->_scenarioFile, $this->_scenarioParams);
    }

    /**
     * @param string $scenarioFile
     * @param array $scenarioParams
     * @param string $expectedExceptionMsg
     * @dataProvider runExceptionDataProvider
     * @expectedException Magento_Exception
     */
    public function testRunException($scenarioFile, array $scenarioParams, $expectedExceptionMsg = '')
    {
        $this->setExpectedException('Magento_Exception', $expectedExceptionMsg);
        $this->_object->run($scenarioFile, $scenarioParams);
    }

    public function runExceptionDataProvider()
    {
        return array(
            'non-existing scenario' => array(
                'non_existing_scenario.jmx',
                $this->_scenarioParams,
            ),
            'no "host" param' => array(
                $this->_scenarioFile,
                array(Magento_Scenario::PARAM_PATH => '/'),
            ),
            'no "path" param' => array(
                $this->_scenarioFile,
                array(Magento_Scenario::PARAM_HOST => '127.0.0.1'),
            ),
            'scenario failure in report' => array(
                __DIR__ . '/_files/scenario_failure.jmx',
                $this->_scenarioParams,
                'fixture failure message',
            ),
            'scenario error in report' => array(
                __DIR__ . '/_files/scenario_error.jmx',
                $this->_scenarioParams,
                'fixture error message',
            ),
        );
    }
}
