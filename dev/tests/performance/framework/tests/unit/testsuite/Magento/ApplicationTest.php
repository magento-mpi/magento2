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

class Magento_ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Performance_Config
     */
    protected $_config;

    /**
     * @var Magento_Shell|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shell;

    /**
     * @var Magento_Application|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var string
     */
    protected $_installerScript;

    /**
     * @var string
     */
    protected $_fixtureDir;

    /**
     * @var array
     */
    protected $_fixtureConfigData;

    /**
     * @var array
     */
    protected  $_fixtureEvents = array();

    protected function setUp()
    {
        $this->_fixtureDir = __DIR__ . '/Performance/_files';
        $this->_fixtureConfigData = require($this->_fixtureDir . '/config_data.php');

        $this->_installerScript = realpath($this->_fixtureDir . '/app_base_dir//dev/shell/install.php');

        $this->_config = new Magento_Performance_Config(
            $this->_fixtureConfigData, $this->_fixtureDir, $this->_fixtureDir . '/app_base_dir'
        );
        $this->_shell = $this->getMock('Magento_Shell', array('execute'));

        $this->_object = $this->getMock(
            'Magento_Application',
            array('_bootstrap', '_cleanupMage', '_reindex', '_updateFilesystemPermissions'),
            array($this->_config, $this->_shell)
        );
        $this->_object->expects($this->any())
            ->method('_reindex')
            ->will($this->returnValue($this->_object));
    }

    protected function tearDown()
    {
        unset($this->_config);
        unset($this->_shell);
        unset($this->_object);
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testConstructorException()
    {
        $invalidAppDir = __DIR__;
        new Magento_Application(
            new Magento_Performance_Config($this->_fixtureConfigData, $this->_fixtureDir, $invalidAppDir),
            $this->_shell
        );
    }

    public function testApplyFixtures()
    {
        $fixturesPath = __DIR__ . '/_files/application_test/';
        $fixture1 = array(
            $fixturesPath . 'fixture1.php'
        );
        $allFixtures = array(
            $fixturesPath . 'fixture1.php',
            $fixturesPath . 'fixture2.php',
        );

        try {
            // Expose itself to fixtures, so they can call addFixtureEvent()
            $GLOBALS['applicationTestForFixtures'] = $this;

            // Test fixture application
            $this->_fixtureEvents = array();
            $this->_object->applyFixtures($fixture1);
            $this->assertEquals(array('fixture1'), $this->_fixtureEvents, 'Fixture is not applied');

            $this->_fixtureEvents = array();
            $this->_object->applyFixtures($allFixtures);
            $this->assertEquals(array('fixture2'), $this->_fixtureEvents, 'One missing fixture must be applied');

            $this->_fixtureEvents = array();
            $this->_object->applyFixtures($fixture1);
            $this->assertEquals(array('fixture1'), $this->_fixtureEvents,
                'Fixture must be re-applied after excessive fixtures');

            unset($GLOBALS['applicationTestForFixtures']);
        } catch (Exception $e) {
            unset($GLOBALS['applicationTestForFixtures']);
            throw $e;
        }
    }

    /**
     * Log event that happened in fixtures.
     * Method is used externally by fixtures, when they are applied (executed).
     *
     * @param string $name
     */
    public function addFixtureEvent($name)
    {
        $this->_fixtureEvents[] = $name;
    }
}
