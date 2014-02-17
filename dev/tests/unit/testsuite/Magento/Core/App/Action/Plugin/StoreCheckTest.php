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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invocationChainMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $this->_storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_invocationChainMock =
            $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->_invocationChainMock->expects($this->once())->method('proceed');
        $this->_plugin = new \Magento\Core\App\Action\Plugin\StoreCheck($this->_storeManagerMock);
    }

    public function testBeforeDispatchWhenStoreNotActive()
    {
        $this->_storeMock->expects($this->any())->method('getIsActive')->will($this->returnValue(false));
        $this->_storeManagerMock->expects($this->once())->method('throwStoreException');
        $this->_plugin->aroundDispatch(array(), $this->_invocationChainMock);
    }

    public function testBeforeDispatchWhenStoreIsActive()
    {
        $this->_storeMock->expects($this->any())->method('getIsActive')->will($this->returnValue(true));
        $this->_storeManagerMock->expects($this->never())->method('throwStoreException');
        $this->_plugin->aroundDispatch(array(), $this->_invocationChainMock);
    }
}
