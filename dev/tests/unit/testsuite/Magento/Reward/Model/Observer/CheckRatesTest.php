<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class CheckRatesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rateFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \Magento\Reward\Model\Observer\CheckRates
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->rateFactoryMock = $this->getMock('\Magento\Reward\Model\Reward\RateFactory', ['create'], [], '', false);
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');

        $this->subject = $objectManager->getObject(
            '\Magento\Reward\Model\Observer\CheckRates',
            [
                'rewardData' => $this->rewardDataMock,
                'storeManager' => $this->storeManagerMock,
                'rateFactory' => $this->rateFactoryMock
            ]
        );
    }

    public function testCheckRatesIfRewardsEnabled()
    {
        $groupId = 1;
        $websiteId = 2;

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getWebsiteId', '__wakeup'], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(true));
        $this->rewardDataMock->expects($this->once())->method('setHasRates')->with(true)->will($this->returnSelf());

        $customerSession = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $customerSession->expects($this->once())->method('getCustomerGroupId')->will($this->returnValue($groupId));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getCustomerSession'], [], '', false);
        $eventMock->expects($this->once())->method('getCustomerSession')->will($this->returnValue($customerSession));

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $rateMock = $this->getMock(
            '\Magento\Reward\Model\Reward\Rate',
            ['fetch', '__wakeup', 'getId', 'reset'],
            [],
            '',
            false
        );
        $this->rateFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rateMock));

        $valueMap = [
            [$groupId, $websiteId, \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY, $rateMock],
            [$groupId, $websiteId, \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS, $rateMock]
        ];

        $rateMock->expects($this->exactly(2))->method('fetch')->will($this->returnValueMap($valueMap));
        $rateMock->expects($this->once())->method('reset')->will($this->returnSelf());
        $rateMock->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testCheckRatesIfRewardsDisabled()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(false));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
