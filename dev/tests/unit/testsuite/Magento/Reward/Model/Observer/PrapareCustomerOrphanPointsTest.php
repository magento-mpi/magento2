<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class PrapareCustomerOrphanPointsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \Magento\Reward\Model\Observer\PrepareCustomerOrphanPoints
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);
        $this->subject = $objectManager->getObject(
            '\Magento\Reward\Model\Observer\PrepareCustomerOrphanPoints',
            ['rewardFactory' => $this->rewardFactoryMock]
        );
    }

    public function testPrepareOrphanPoints()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getWebsite'], [], '', false);
        $eventMock->expects($this->once())->method('getWebsite')->will($this->returnValue($websiteMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $rewardMock = $this->getMock('\Magento\Reward\Model\Reward', [], [], '', false);
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $websiteMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $websiteMock->expects($this->once())->method('getBaseCurrencyCode')->will($this->returnValue('currencyCode'));

        $rewardMock->expects($this->once())
            ->method('prepareOrphanPoints')
            ->with(1, 'currencyCode')
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
