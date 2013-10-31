<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Install\App;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\App\Console
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Install\Model\Installer\Console
     */
    protected $_installerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\Dir\Verification
     */
    protected $_dirVerifierMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Install\App\Output
     */
    protected $_outputMock;

    /** \PHPUnit_Framework_MockObject_MockObject|\Magento\Install\Model\Installer\ConsoleFactory */
    protected $_installerFactoryMock;

    protected function setUp()
    {
        $this->_installerFactoryMock = $this->getMock('\Magento\Install\Model\Installer\ConsoleFactory',
            array('create'), array(), '', false);
        $this->_installerMock = $this->getMock('Magento\Install\Model\Installer\Console', array(), array(), '', false);
        $this->_dirVerifierMock = $this->getMock('Magento\App\Dir\Verification', array(), array(), '', false);
        $this->_outputMock = $this->getMock('Magento\Install\App\Output', array(), array(), '', false);

        $this->_installerFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->_installerMock));
    }

    protected function _createModel($params = array())
    {
        return new \Magento\Install\App\Console($this->_installerFactoryMock, $params, $this->_outputMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_configMock);
        unset($this->_installerMock);
        unset($this->_installerFactoryMock);
        unset($this->_dirVerifierMock);
    }

    /**
     * @param string $param
     * @param string $method
     * @param string $testValue
     * @dataProvider executeShowsRequestedDataProvider
     */
    public function testExecuteShowsRequestedData($param, $method, $testValue)
    {
        $model = $this->_createModel(array($param => true));
        $this->_installerMock
            ->expects($this->once())
            ->method($method)
            ->will($this->returnValue($testValue));
        $this->_outputMock->expects($this->once())->method('export')->with($testValue);
        $model->execute();
    }

    public function executeShowsRequestedDataProvider()
    {
        return array(
            array('show_locales', 'getAvailableLocales', 'locales'),
            array('show_currencies', 'getAvailableCurrencies', 'currencies'),
            array('show_timezones', 'getAvailableTimezones', 'timezones'),
            array('show_install_options', 'getAvailableInstallOptions', 'install_options'),
        );
    }

    public function testInstallReportsSuccessMessage()
    {
        $model = $this->_createModel(array());
        $this->_outputMock->expects($this->once())->method('success')->with($this->stringContains('successfully'));
        $model->execute();
    }

    public function testInstallReportsEncryptionKey()
    {
        $model = $this->_createModel(array());
        $this->_installerMock->expects($this->once())->method('install')->will($this->returnValue('enc_key'));
        $this->_outputMock->expects($this->once())->method('success')->with($this->stringContains('enc_key'));
        $model->execute();
    }

    public function testUninstallReportsSuccess()
    {
        $model = $this->_createModel(array('uninstall' => true));
        $this->_installerMock->expects($this->once())->method('uninstall')->will($this->returnValue(true));
        $this->_outputMock->expects($this->once())->method('success')->with($this->stringContains('Uninstalled'));
        $model->execute();
    }

    public function testUninstallReportsIgnoreIfApplicationIsNotInstalled()
    {
        $model = $this->_createModel(array('uninstall' => true));
        $this->_installerMock->expects($this->once())->method('uninstall')->will($this->returnValue(false));
        $this->_outputMock->expects($this->once())->method('success')->with($this->stringContains('non-installed'));
        $model->execute();
    }

    public function testExecuteReportsErrors()
    {
        $model = $this->_createModel(array('uninstall' => true));
        $this->_installerMock->expects($this->once())->method('hasErrors')->will($this->returnValue(true));
        $this->_installerMock->expects($this->once())->method('getErrors')->will($this->returnValue(array('error1')));
        $this->_outputMock->expects($this->once())->method('error')->with($this->stringContains('error1'));
        $model->execute();
    }

    public function testExecuteLoadsExtraConfig()
    {
        $model = $this->_createModel(array('config' => realpath(__DIR__ . '/_files/config.php')));
        $this->_installerMock->expects($this->once())->method('uninstall')->will($this->returnValue(true));
        $model->execute();
    }
}
