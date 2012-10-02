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
     * @var Magento_Config|PHPUnit_Framework_MockObject_MockObject
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

    protected function setUp()
    {
        $fixtureDir = __DIR__ . '/_files';
        $configData = require($fixtureDir . '/config_data.php');
        $logger = new Zend_Log(new Zend_Log_Writer_Null);

        $this->_installerScript = realpath($fixtureDir . '/dev/shell/install.php');

        $this->_config = $this->getMock('Magento_Config',
            array('getApplicationBaseDir'),
            array($configData, $fixtureDir, $logger)
        );
        $this->_config->expects($this->any())
            ->method('getApplicationBaseDir')
            ->will($this->returnValue($fixtureDir));

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
     * @param Magento_Config $config
     * @dataProvider constructorExceptionDataProvider
     * @expectedException Magento_Exception
     */
    public function testConstructorException($config)
    {
        new Magento_Application($config, $this->_shell);
    }

    public function constructorExceptionDataProvider()
    {
        $config = $this->getMock('Magento_Config', array('getApplicationBaseDir'), array(), '', false);
        $config->expects($this->any())
            ->method('getApplicationBaseDir')
            ->will($this->returnValue(realpath(__DIR__)));

        return array(array($config));
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
        $this->_object->install(array('option1' => 'value1', 'option2' => 'value 2'));
    }
}
