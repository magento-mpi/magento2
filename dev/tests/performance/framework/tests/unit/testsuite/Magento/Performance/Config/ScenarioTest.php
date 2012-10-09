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

class Magento_Performance_Config_ScenarioTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_fixtureDir;

    /**
     * @var Magento_Performance_Config_Scenario
     */
    protected $_object;

    public function setUp()
    {
        $this->_fixtureDir = __DIR__ . '/_files';
        $this->_object = $this->_createObjectFromConfig('config.php');
    }

    public function tearDown()
    {
        unset($this->_object);
    }

    /**
     * Create scenario config object by loading data from fixture file
     *
     * @param string $file
     * @return Magento_Performance_Config_Scenario
     */
    public function _createObjectFromConfig($file)
    {
        $config = require($this->_fixtureDir . '/' . $file);
        $object = new Magento_Performance_Config_Scenario($config['title'], $config['config'],
            $config['defaultConfig'], $config['fixedArguments'], $this->_fixtureDir);
        return $object;
    }

    public function testGetTitle()
    {
        $this->assertEquals('Test title', $this->_object->getTitle());
    }

    public function testGetFile()
    {
        $expectedFile = realpath($this->_fixtureDir . '/scenarios/test.php');
        $this->assertEquals($expectedFile, $this->_object->getFile());
    }

    public function testGetSettings()
    {
        $expectedArguments = array(
            'setting1' => 'valueOverriden',
            'setting2' => 'value2',
            'setting3' => 'value3',
        );
        $this->assertEquals($expectedArguments, $this->_object->getSettings());
    }

    public function testGetArguments()
    {
        $expectedArguments = array(
            Magento_Performance_Config_Scenario::ARG_USERS => 10,
            Magento_Performance_Config_Scenario::ARG_LOOPS => 1,
            'arg' => 'val',
            'argDefault' => 'valueDefault',
            'fixedArg' => 'fixedValue',
        );
        $this->assertEquals($expectedArguments, $this->_object->getArguments());
    }

    public function testGetFixtures()
    {
        $expectedFixtures = array(
            realpath($this->_fixtureDir . '/fixtures/fixture1.php'),
            realpath($this->_fixtureDir . '/fixtures/fixture2.php'),
            realpath($this->_fixtureDir . '/fixtures/fixture3.php'),
        );
        $this->assertEquals($expectedFixtures, $this->_object->getFixtures());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadUsersException()
    {
        $this->_createObjectFromConfig('config_bad_users.php');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadLoopsException()
    {
        $this->_createObjectFromConfig('config_bad_loops.php');
    }

    public function testUsersLoopsAdded()
    {
        $object = $this->_createObjectFromConfig('config_users_loops_added.php');
        $arguments = $object->getArguments();

        $this->assertArrayHasKey('users', $arguments);
        $this->assertEquals(1, $arguments['users']);
        $this->assertArrayHasKey('loops', $arguments);
        $this->assertEquals(1, $arguments['loops']);
    }

    public function testFileDirectPermitted()
    {
        $object = $this->_createObjectFromConfig('config_file_direct.php');
        $expectedFile = realpath($this->_fixtureDir . '/scenarios/test.php');
        $this->assertEquals($expectedFile, $object->getFile());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNoTitleException()
    {
        $this->_createObjectFromConfig('config_no_title_defined.php');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNoFileException()
    {
        $this->_createObjectFromConfig('config_no_file_defined.php');
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testNonExistingFileException()
    {
        $this->_createObjectFromConfig('config_non_existing_file.php');
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testNonExistingFixtureException()
    {
        $this->_createObjectFromConfig('config_non_existing_fixture.php');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFixturesFormatException()
    {
        $this->_createObjectFromConfig('config_invalid_fixtures_format.php');
    }
}
