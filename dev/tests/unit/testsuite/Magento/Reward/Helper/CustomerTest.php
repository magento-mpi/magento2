<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Helper;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Reward\Helper\Customer
     */
    protected $subject;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $contextMock = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManagerHelper->getObject(
            '\Magento\Reward\Helper\Customer',
            ['storeManager' => $this->storeManagerMock, 'context' => $contextMock]
        );
    }

    public function testGetUnsubscribeUrlIfNotificationDisabled()
    {
        $storeId = 100;
        $url = 'unsubscribe_url';
        $params = ['store_id' => $storeId];

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())
            ->method('getUrl')
            ->with('magento_reward/customer/unsubscribe/', $params)
            ->willReturn($url);

        $this->storeManagerMock->expects($this->once())->method('getStore')->with($storeId)->willReturn($storeMock);
        $this->assertEquals($url, $this->subject->getUnsubscribeUrl(false, $storeId));
    }

    public function testGetUnsubscribeUrlIfNotificationEnabled()
    {
        $storeId = 100;
        $url = 'unsubscribe_url';
        $params = ['store_id' => $storeId, 'notification' => true];

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())
            ->method('getUrl')
            ->with('magento_reward/customer/unsubscribe/', $params)
            ->willReturn($url);

        $this->storeManagerMock->expects($this->once())->method('getStore')->with($storeId)->willReturn($storeMock);
        $this->assertEquals($url, $this->subject->getUnsubscribeUrl(true, $storeId));
    }

    public function testGetUnsubscribeUrlIfStoreIdNotSet()
    {
        $url = 'unsubscribe_url';
        $params = ['notification' => true];

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())
            ->method('getUrl')
            ->with('magento_reward/customer/unsubscribe/', $params)
            ->willReturn($url);

        $this->storeManagerMock->expects($this->once())->method('getStore')->with(null)->willReturn($storeMock);
        $this->assertEquals($url, $this->subject->getUnsubscribeUrl(true));
    }
}
 