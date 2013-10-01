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
    protected $_tmpConfigFile = '';

    /**
     * @var \Magento\Install\Model\Installer\Config
     */
    protected $_model;

    /**
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    protected function setUp()
    {
        $this->_tmpConfigFile = TESTS_TEMP_DIR . DIRECTORY_SEPARATOR . 'local.xml';
        $this->_filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_model = new \Magento\Install\Model\Installer\Config(
            $this->getMock('Magento\Install\Model\InstallerProxy', array(), array(),
                '', false),
            $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false),
            new \Magento\Core\Model\Dir(__DIR__, array(), array(\Magento\Core\Model\Dir::CONFIG => TESTS_TEMP_DIR)),
            $this->_filesystemMock,
            $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false)
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
        $keyPlaceholder = \Magento\Install\Model\Installer\Config::TMP_ENCRYPT_KEY_VALUE;
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
