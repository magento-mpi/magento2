<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Install_Model_Installer_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_tmpConfigFile = '';

    /**
     * @var Magento_Install_Model_Installer_Config
     */
    protected $_model;

    /**
     * @var Magento_Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    protected function setUp()
    {
        $this->_tmpConfigFile = TESTS_TEMP_DIR . DIRECTORY_SEPARATOR . 'local.xml';
        $this->_filesystemMock = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_model = new Magento_Install_Model_Installer_Config(
            $this->getMock('Magento_Install_Model_Installer', array(), array(),
                'Magento_Install_Model_InstallerProxy', false),
            $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false),
            new Magento_Core_Model_Dir(__DIR__, array(), array(Magento_Core_Model_Dir::CONFIG => TESTS_TEMP_DIR)),
            $this->getMock('Magento_Core_Model_Config_Resource', array(), array(), '', false),
            $this->_filesystemMock,
            $this->getMock('Magento_Core_Model_StoreManagerInterface', array(), array(), '', false),
            new Magento_Filesystem(new Magento_Filesystem_Adapter_Local())
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testReplaceTmpInstallDate()
    {
        $datePlaceholder = Magento_Install_Model_Installer_Config::TMP_INSTALL_DATE_VALUE;
        $fixtureConfigData = "<date>$datePlaceholder</date>";
        $expectedConfigData = '<date>Sat, 19 Jan 2013 18:50:39 -0800</date>';

        $this->_filesystemMock->expects($this->once())
            ->method('read')
            ->with($this->equalTo($this->_tmpConfigFile))
            ->will($this->returnValue($fixtureConfigData));
        $this->_filesystemMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo($this->_tmpConfigFile), $this->equalTo($expectedConfigData))
            ->will($this->returnValue($fixtureConfigData));

        $this->_model->replaceTmpInstallDate('Sat, 19 Jan 2013 18:50:39 -0800');
    }

    public function testReplaceTmpEncryptKey()
    {
        $keyPlaceholder = Magento_Install_Model_Installer_Config::TMP_ENCRYPT_KEY_VALUE;
        $fixtureConfigData = "<key>$keyPlaceholder</key>";
        $expectedConfigData = '<key>3c7cf2e909fd5e2268a6e1539ae3c835</key>';

        $this->_filesystemMock->expects($this->once())
            ->method('read')
            ->with($this->equalTo($this->_tmpConfigFile))
            ->will($this->returnValue($fixtureConfigData));
        $this->_filesystemMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo($this->_tmpConfigFile), $this->equalTo($expectedConfigData))
            ->will($this->returnValue($fixtureConfigData));

        $this->_model->replaceTmpEncryptKey('3c7cf2e909fd5e2268a6e1539ae3c835');
    }
}
