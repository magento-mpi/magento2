<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Action\Plugin;

class StoreTest extends \PHPUnit_Framework_TestCase
{

    public function testAroundDispatch()
    {
        $storeManagerMock = $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false);
        $invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $plugin = new \Magento\Backend\App\Action\Plugin\Store($storeManagerMock);
        $storeManagerMock->expects($this->once())->method('setCurrentStore')->with('admin');
        $invocationChainMock->expects($this->once())->method('proceed')
            ->with(array())->will($this->returnValue('expected'));
        $this->assertEquals('expected', $plugin->aroundDispatch(array(), $invocationChainMock));
    }
}