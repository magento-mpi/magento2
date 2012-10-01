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
    protected $_scenarioArgs = array(
        'arguments' => array(
            Magento_Scenario::ARGUMENT_HOST  => '127.0.0.1',
            Magento_Scenario::ARGUMENT_PATH  => '/',
            Magento_Scenario::ARGUMENT_USERS => 2,
        ),
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
        $this->_object->run($this->_scenarioFile, $this->_scenarioArgs);
    }

    /**
     * @param string $scenarioFile
     * @param array $scenarioArgs
     * @param string $expectedExceptionMsg
     * @dataProvider runExceptionDataProvider
     * @expectedException Magento_Exception
     */
    public function testRunException($scenarioFile, array $scenarioArgs, $expectedExceptionMsg = '')
    {
        $this->setExpectedException('Magento_Exception', $expectedExceptionMsg);
        $this->_object->run($scenarioFile, $scenarioArgs);
    }

    public function runExceptionDataProvider()
    {
        return array(
            'non-existing scenario' => array(
                'non_existing_scenario.jmx',
                $this->_scenarioArgs,
            ),
            'no "host" argument' => array(
                $this->_scenarioFile,
                array(Magento_Scenario::ARGUMENT_PATH => '/'),
            ),
            'no "path" argument' => array(
                $this->_scenarioFile,
                array(Magento_Scenario::ARGUMENT_HOST => '127.0.0.1'),
            ),
            'scenario failure in report' => array(
                __DIR__ . '/_files/scenario_failure.jmx',
                $this->_scenarioArgs,
                'fixture failure message',
            ),
            'scenario error in report' => array(
                __DIR__ . '/_files/scenario_error.jmx',
                $this->_scenarioArgs,
                'fixture error message',
            ),
        );
    }

    /**
     * Test run() method with different 'dry run' settings
     *
     * @param array $scenarioArgs
     * @param int $runTimes
     *
     * @dataProvider dryRunDataProvider
     */
    public function testDryRun($scenarioArgs, $runTimes)
    {
        $this->_shell
            ->expects($this->exactly($runTimes))
            ->method('execute');

        $this->_object->run($this->_scenarioFile, $scenarioArgs);
    }

    public function dryRunDataProvider()
    {
        return array(
            'dry run not skipped by default' => array(
                $this->_scenarioArgs,
                2,
            ),
            'dry run not skipped in config' => array(
                array_merge_recursive($this->_scenarioArgs, array(
                    'settings' => array(
                        'skip_dry_run' => false,
                    ),
                )),
                2,
            ),
            'dry run skipped' => array(
                array_merge_recursive($this->_scenarioArgs, array(
                    'settings' => array(
                        'skip_dry_run' => true,
                    ),
                )),
                1,
            ),
        );
    }
}
