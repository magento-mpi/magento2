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

    protected function setUp()
    {
        $this->_fixtureDir = __DIR__ . '/Performance/_files';
        $this->_fixtureConfigData = require($this->_fixtureDir . '/config_data.php');

        $this->_installerScript = realpath($this->_fixtureDir . '/dev/shell/install.php');

        $this->_config = new Magento_Performance_Config(
            $this->_fixtureConfigData, $this->_fixtureDir, $this->_fixtureDir
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

    public function testInstall()
    {
        $this->_shell
            ->expects($this->at(1))
            ->method('execute')
            ->with(
                // @codingStandardsIgnoreStart
                'php -f %s -- --option1 %s --option2 %s --url %s --secure_base_url %s --admin_frontname %s --admin_username %s --admin_password %s',
                // @codingStandardsIgnoreEnd
                array(
                    $this->_installerScript,
                    'value 1',
                    'value 2',
                    'http://127.0.0.1/',
                    'http://127.0.0.1/',
                    'backend',
                    'admin',
                    'password1',
                )
            )
        ;
        $this->_object
            ->expects($this->once())
            ->method('_reindex')
        ;
        $this->_object->install();
    }
}
