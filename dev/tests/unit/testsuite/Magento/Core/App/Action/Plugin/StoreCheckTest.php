<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Action\Plugin;

class StoreCheckTests extends \PHPUnit_Framework_TestCase
{
    public function testBeforeDispatch()
    {
        $storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManagerInterface');
        $plugin = new \Magento\Core\App\Action\Plugin\StoreCheck($storeManagerMock);
        $storeMock = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getIsActive')->will($this->returnValue(false));
        $storeManagerMock
            ->expects($this->once())
            ->method('throwStoreException')
            ->will($this->returnValue(new \Magento\Core\Model\Store\Exception));
        $plugin->beforeDispatch();
    }
}