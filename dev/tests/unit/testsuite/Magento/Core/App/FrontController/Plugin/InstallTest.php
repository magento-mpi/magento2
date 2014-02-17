<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;
class InstallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Module\FrontController\Plugin\Install
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbUpdaterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invocationChainMock;

    protected function setUp()
    {
        $this->_appStateMock = $this->getMock('\Magento\App\State', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('\Magento\Cache\FrontendInterface');
        $this->_dbUpdaterMock = $this->getMock('\Magento\Module\UpdaterInterface');
        $this->_invocationChainMock =
            $this->getMock('\Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->_model = new \Magento\Module\FrontController\Plugin\Install(
            $this->_appStateMock,
            $this->_cacheMock,
            $this->_dbUpdaterMock
        );
    }

    public function testAroundDispatch()
    {
        $arguments = array();
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_cacheMock
            ->expects($this->once())->method('load')->with('data_upgrade')->will($this->returnValue(false));
        $this->_dbUpdaterMock->expects($this->once())->method('updateScheme');
        $this->_dbUpdaterMock->expects($this->once())->method('updateData');
        $this->_cacheMock->expects($this->once())->method('save')->with('true', 'data_upgrade');
        $this->_invocationChainMock->expects($this->once())->method('proceed')->with($arguments);
        $this->_model->aroundDispatch($arguments, $this->_invocationChainMock);
    }
}
