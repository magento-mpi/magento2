<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class RevertRewardPointsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reverterMock;

    /**
     * @var \Magento\Reward\Model\Observer\RevertRewardPoints
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->reverterMock = $this->getMock('\Magento\Reward\Model\Reward\Reverter', [], [], '', false);
        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\RevertRewardPoints',
            ['reverter' => $this->reverterMock]
        );
    }

    public function testRevertRewardPointsIfOrderIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue(null));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testRevertRewardPoints()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->reverterMock->expects($this->once())
            ->method('revertRewardPointsForOrder')
            ->with($orderMock)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
 