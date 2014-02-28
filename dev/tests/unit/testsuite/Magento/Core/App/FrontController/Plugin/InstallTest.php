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
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    protected function setUp()
    {
        $this->_appStateMock = $this->getMock('\Magento\App\State', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('\Magento\Cache\FrontendInterface');
        $this->_dbUpdaterMock = $this->getMock('\Magento\Module\UpdaterInterface');
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->requestMock = $this->getMock('Magento\App\RequestInterface');
        $this->subjectMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->_model = new \Magento\Module\FrontController\Plugin\Install(
            $this->_appStateMock,
            $this->_cacheMock,
            $this->_dbUpdaterMock
        );
    }

    public function testAroundDispatch()
    {
        $this->_appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_cacheMock
            ->expects($this->once())->method('load')->with('data_upgrade')->will($this->returnValue(false));
        $this->_dbUpdaterMock->expects($this->once())->method('updateScheme');
        $this->_dbUpdaterMock->expects($this->once())->method('updateData');
        $this->_cacheMock->expects($this->once())->method('save')->with('true', 'data_upgrade');
        $this->assertEquals('Expected',
            $this->_model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock));
    }
}
