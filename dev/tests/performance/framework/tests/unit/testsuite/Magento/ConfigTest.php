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

class Magento_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Config
     */
    protected $_object;

    /**
     * @var array
     */
    protected $_sampleConfigData = array(
        'application' => array(
            'url_host' => '127.0.0.1',
            'url_path' => '/',
            'installation' => array(
                'options' => array(
                    'option1' => 'value 1',
                    'option2' => 'value 2',
                ),
                'fixture_files' => '{fixture}.php',
            ),
        ),
        'scenario' => array(
            'files' => '*.jmx',
            'common_params' => array(
                'param1' => 'value 1',
                'param2' => 'value 2',
            ),
            'scenario_params' => array(
                'scenario.jmx' => array(
                    'param2' => 'overridden value 2',
                ),
            ),
        ),
        'report_dir' => 'report',
    );

    protected function setUp()
    {
        $this->_object = new Magento_Config($this->_sampleConfigData, __DIR__ . '/_files');
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @param array $configData
     * @param string $baseDir
     * @param string $expectedExceptionMsg
     */
    public function testConstructorException(array $configData, $baseDir, $expectedExceptionMsg)
    {
        $this->setExpectedException('Magento_Exception', $expectedExceptionMsg);
        new Magento_Config($configData, $baseDir);
    }

    /**
     * @return array
     */
    public function constructorExceptionDataProvider()
    {
        return array(
            'non-existing base dir' => array(
                $this->_sampleConfigData,
                'non_existing_dir',
                "Base directory 'non_existing_dir' does not exist",
            ),
            'no scenarios match pattern' => array(
                array_merge($this->_sampleConfigData, array('scenario' => array('files' => 'non_existing_*.jmx'))),
                __DIR__ . '/_files',
                'No scenario files match',
            ),
        );
    }

    public function testGetApplicationUrlHost()
    {
        $this->assertEquals('127.0.0.1', $this->_object->getApplicationUrlHost());
    }

    public function testGetApplicationUrlPath()
    {
        $this->assertEquals('/', $this->_object->getApplicationUrlPath());
    }

    public function testGetInstallOptions()
    {
        $expectedOptions = array('option1' => 'value 1', 'option2' => 'value 2');
        $this->assertEquals($expectedOptions, $this->_object->getInstallOptions());
    }

    public function testGetScenarios()
    {
        $dir = str_replace('\\', '/', __DIR__ . '/_files');
        $expectedScenarios = array(
            $dir . '/scenario.jmx' => array(
                'param1' => 'value 1',
                'param2' => 'overridden value 2',
            ),
            $dir . '/scenario_error.jmx' => array(
                'param1' => 'value 1',
                'param2' => 'value 2',
            ),
            $dir . '/scenario_failure.jmx' => array(
                'param1' => 'value 1',
                'param2' => 'value 2',
            ),
        );

        $actualScenarios = $this->_object->getScenarios();
        ksort($actualScenarios);
        $this->assertEquals($expectedScenarios, $actualScenarios);
    }

    public function testGetFixtureFiles()
    {
        $expectedFixtureFile = str_replace('\\', '/', __DIR__ . '/_files/fixture.php');
        $this->assertEquals(array($expectedFixtureFile), $this->_object->getFixtureFiles());
    }

    public function testGetReportDir()
    {
        $expectedReportDir = str_replace('\\', '/', __DIR__ . '/_files/report');
        $this->assertEquals($expectedReportDir, $this->_object->getReportDir());
    }

    public function testGetJMeterPath()
    {
        $oldEnv = getenv("jmeter_jar_file");
        try {
            $baseDir = __DIR__ . '/_files';
            $expectedPath = '/path/to/custom/JMeterFile.jar';

            $configData = $this->_sampleConfigData;
            $configData['jmeter_jar_file'] = $expectedPath;
            $object = new Magento_Config($configData, $baseDir);
            $this->assertEquals($expectedPath, $object->getJMeterPath());

            $configData['jmeter_jar_file'] = null;
            putenv("jmeter_jar_file={$expectedPath}");
            $object = new Magento_Config($configData, $baseDir);
            $this->assertEquals($expectedPath, $object->getJMeterPath());

            putenv('jmeter_jar_file=');
            $object = new Magento_Config($configData, $baseDir);
            $this->assertNotEmpty($object->getJMeterPath());
        } catch (Exception $e) {
            putenv("jmeter_jar_file={$oldEnv}");
            throw $e;
        }
        putenv("jmeter_jar_file={$oldEnv}");
    }
}
