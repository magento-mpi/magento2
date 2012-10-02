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
    protected $_sampleConfigData;

    protected function setUp()
    {
        $this->_object = new Magento_Config($this->_getSampleConfigData(), __DIR__ . DIRECTORY_SEPARATOR . '_files');
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * Return default sample config data
     *
     * @return array
     */
    protected function _getSampleConfigData()
    {
        if (!$this->_sampleConfigData) {
            $this->_sampleConfigData = require(__DIR__ . '/_files/config_data.php');
        }
        return $this->_sampleConfigData;
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @param array $configData
     * @param string $baseDir
     * @param string $expectedException
     * @param string $expectedExceptionMsg
     */
    public function testConstructorException(array $configData, $baseDir, $expectedException, $expectedExceptionMsg)
    {
        $this->setExpectedException($expectedException, $expectedExceptionMsg);
        new Magento_Config($configData, $baseDir);
    }

    /**
     * @return array
     */
    public function constructorExceptionDataProvider()
    {
        $invalidFormat = $this->_getSampleConfigData();
        $invalidFormat['scenario']['scenarios'] = 'string_scenarios_*.jmx';

        $nonExistingScenario = $this->_getSampleConfigData();
        $nonExistingScenario['scenario']['scenarios'] = array('non_existing_scenario.jmx');

        $invalidFixtureFormat = $this->_getSampleConfigData();
        $invalidFixtureFormat['scenario']['scenarios']['scenario.jmx']['fixtures'] = 'string_fixtures_*.php';

        $nonExistingFixture = $this->_getSampleConfigData();
        $nonExistingFixture['scenario']['scenarios']['scenario.jmx']['fixtures'][] = 'non_existing_fixture.php';

        return array(
            'non-existing base dir' => array(
                $this->_getSampleConfigData(),
                'non_existing_dir',
                'Magento_Exception',
                "Base directory 'non_existing_dir' does not exist",
            ),
            'invalid scenarios format' => array(
                $invalidFormat,
                __DIR__ . DIRECTORY_SEPARATOR . '_files',
                'InvalidArgumentException',
                "'scenario' => 'scenarios' option must be an array",
            ),
            'non-existing scenario' => array(
                $nonExistingScenario,
                __DIR__ . DIRECTORY_SEPARATOR . '_files',
                'Magento_Exception',
                "Scenario 'non_existing_scenario.jmx' doesn't exist",
            ),
            'invalid fixtures format' => array(
                $invalidFixtureFormat,
                __DIR__ . DIRECTORY_SEPARATOR . '_files',
                'InvalidArgumentException',
                "Scenario 'fixtures' option must be an array, not a value: 'string_fixtures_*.php'",
            ),
            'non-existing fixture' => array(
                $nonExistingFixture,
                __DIR__ . DIRECTORY_SEPARATOR . '_files',
                'Magento_Exception',
                "Fixture 'non_existing_fixture.php' doesn't exist",
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

    public function testGetAdminOptions()
    {
        $expectedOptions = array(
            'frontname' => 'backend',
            'username' => 'admin',
            'password' => 'password1',
        );
        $this->assertEquals($expectedOptions, $this->_object->getAdminOptions());
    }

    public function testGetInstallOptions()
    {
        $expectedOptions = array('option1' => 'value 1', 'option2' => 'value 2');
        $this->assertEquals($expectedOptions, $this->_object->getInstallOptions());
    }

    public function testGetScenarios()
    {
        $templateScenario = array(
            'arguments' => array(
                'host' => '127.0.0.1',
                'path' => '/',
                'admin_frontname' => 'backend',
                'admin_username' => 'admin',
                'admin_password' => 'password1',
                'arg1' => 'value 1',
                'arg2' => 'value 2',
            ),
            'settings' => array(
                'setting1' => 'setting 1',
                'setting2' => 'setting 2',
            ),
            'fixtures' => array(),
        );
        $overridenScenario = $templateScenario;
        $overridenScenario['arguments']['arg2'] = 'overridden value 2';
        $overridenScenario['arguments']['arg3'] = 'custom value 3';
        $overridenScenario['fixtures'] = array(realpath(__DIR__ . DIRECTORY_SEPARATOR . '_files/fixture.php'));

        $dir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $expectedScenarios = array(
            $dir . 'scenario.jmx' => $overridenScenario,
            $dir . 'scenario_error.jmx' => $templateScenario,
            $dir . 'scenario_failure.jmx' => $templateScenario
        );

        $actualScenarios = $this->_object->getScenarios();
        ksort($actualScenarios);
        $this->assertEquals($expectedScenarios, $actualScenarios);
    }

    public function testGetReportDir()
    {
        $expectedReportDir = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'report';
        $this->assertEquals($expectedReportDir, $this->_object->getReportDir());
    }

    public function testGetJMeterPath()
    {
        $oldEnv = getenv("jmeter_jar_file");
        try {
            $baseDir = __DIR__ . '/_files';
            $expectedPath = '/path/to/custom/JMeterFile.jar';

            $configData = $this->_getSampleConfigData();
            $configData['scenario']['jmeter_jar_file'] = $expectedPath;
            $object = new Magento_Config($configData, $baseDir);
            $this->assertEquals($expectedPath, $object->getJMeterPath());

            $configData['scenario']['jmeter_jar_file'] = '';
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
