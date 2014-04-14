<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\App\Action\Plugin;

class StoreCheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\App\Action\Plugin\StoreCheck
     */
    protected $_plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

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
    protected $appStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->_storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock->expects(
            $this->any()
        )->method(
            'getStore'
        )->will(
            $this->returnValue($this->_storeMock)
        );
        $this->subjectMock = $this->getMock('Magento\App\Action\Action', array(), array(), '', false);
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->requestMock = $this->getMock('Magento\App\RequestInterface');
        $this->appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);

        $this->_plugin = new \Magento\Store\App\Action\Plugin\StoreCheck($this->_storeManagerMock, $this->appStateMock);
    }

    /**
     * @expectedException \Magento\Store\Model\Exception
     * @expectedExceptionMessage Current store is not active.
     */
    public function testAroundDispatchWhenStoreNotActiveAppInstalled()
    {
        $this->appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_storeMock->expects($this->any())->method('getIsActive')->will($this->returnValue(false));
        $this->assertEquals(
            'Expected',
            $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchWhenStoreIsActiveAppInstalled()
    {
        $this->appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_storeMock->expects($this->any())->method('getIsActive')->will($this->returnValue(true));
        $this->assertEquals(
            'Expected',
            $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchWhenStoreNotActiveAppNotInstalled()
    {
        $this->appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_storeMock->expects($this->never())->method('getIsActive');
        $this->assertEquals(
            'Expected',
            $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchWhenStoreIsActiveAppNotInstalled()
    {
        $this->appStateMock->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_storeMock->expects($this->never())->method('getIsActive');
        $this->assertEquals(
            'Expected',
            $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}
