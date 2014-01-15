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

namespace Magento\Install\Model\Installer;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_tmpConfigFile = 'local.xml';

    /**
     * @var \Magento\Install\Model\Installer\Config
     */
    protected $_model;

    /**
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var \Magento\Filesystem\Directory\Write|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_directoryMock;

    /**
     * @var \Magento\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_messageManager;

    protected function setUp()
    {
        $this->_directoryMock = $this->getMock('Magento\Filesystem\Directory\Write', array(), array(), '', false);

        $this->_filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_filesystemMock->expects($this->any())
            ->method('getPath')
            ->with(\Magento\Filesystem::CONFIG)
            ->will($this->returnValue(TESTS_TEMP_DIR));
        $this->_filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($this->_directoryMock));

        $this->_messageManager = $this->getMock('\Magento\Message\ManagerInterface', array(), array(), '', false);
        $this->_model = new \Magento\Install\Model\Installer\Config(
            $this->getMock('Magento\Install\Model\Installer', array(), array(), '', false),
            $this->getMock('Magento\App\RequestInterface', array(), array(), '', false),
            $this->_filesystemMock,
            $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false),
            $this->_messageManager
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testReplaceTmpInstallDate()
    {
        $datePlaceholder = \Magento\Install\Model\Installer\Config::TMP_INSTALL_DATE_VALUE;
        $fixtureConfigData = "<date>$datePlaceholder</date>";
        $expectedConfigData = '<date>Sat, 19 Jan 2013 18:50:39 -0800</date>';

        $this->_directoryMock->expects($this->once())
            ->method('readFile')
            ->with($this->equalTo($this->_tmpConfigFile))
            ->will($this->returnValue($fixtureConfigData));
        $this->_directoryMock->expects($this->once())
            ->method('writeFile')
            ->with($this->equalTo($this->_tmpConfigFile), $this->equalTo($expectedConfigData))
            ->will($this->returnValue($fixtureConfigData));

        $this->_model->replaceTmpInstallDate('Sat, 19 Jan 2013 18:50:39 -0800');
    }

    public function testReplaceTmpEncryptKey()
    {
        $keyPlaceholder = \Magento\Install\Model\Installer\Config::TMP_ENCRYPT_KEY_VALUE;
        $fixtureConfigData = "<key>$keyPlaceholder</key>";
        $expectedConfigData = '<key>3c7cf2e909fd5e2268a6e1539ae3c835</key>';

        $this->_directoryMock->expects($this->once())
            ->method('readFile')
            ->with($this->equalTo($this->_tmpConfigFile))
            ->will($this->returnValue($fixtureConfigData));
        $this->_directoryMock->expects($this->once())
            ->method('writeFile')
            ->with($this->equalTo($this->_tmpConfigFile), $this->equalTo($expectedConfigData))
            ->will($this->returnValue($fixtureConfigData));

        $this->_model->replaceTmpEncryptKey('3c7cf2e909fd5e2268a6e1539ae3c835');
    }
}
