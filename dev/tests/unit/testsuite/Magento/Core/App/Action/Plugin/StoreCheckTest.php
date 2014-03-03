<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Action\Plugin;

class StoreCheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\Action\Plugin\StoreCheck
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
    protected $requestMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $this->_storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->subjectMock = $this->getMock('Magento\App\Action\Action', array(), array(), '', false);
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->requestMock = $this->getMock('Magento\App\RequestInterface');
        $this->_plugin = new \Magento\Core\App\Action\Plugin\StoreCheck($this->_storeManagerMock);
    }

    public function testAroundDispatchWhenStoreNotActive()
    {
        $this->_storeMock->expects($this->any())->method('getIsActive')->will($this->returnValue(false));
        $this->_storeManagerMock->expects($this->once())->method('throwStoreException');
        $this->assertEquals('Expected',
            $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock));
    }

    public function testAroundDispatchWhenStoreIsActive()
    {
        $this->_storeMock->expects($this->any())->method('getIsActive')->will($this->returnValue(true));
        $this->_storeManagerMock->expects($this->never())->method('throwStoreException');
        $this->assertEquals('Expected',
            $this->_plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock));
    }
}
