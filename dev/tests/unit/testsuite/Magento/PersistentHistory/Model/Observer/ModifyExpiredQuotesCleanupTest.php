<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

class ModifyExpiredQuotesCleanupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PersistentHistory\Model\Observer\ModifyExpiredQuotesCleanup
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject(
            '\Magento\PersistentHistory\Model\Observer\ModifyExpiredQuotesCleanup'
        );
    }

    public function testModifyExpiredQuotesCleanup()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $salesObserverMock = $this->getMock('\Magento\Sales\Model\Observer', [], [], '', false);

        $salesObserverMock->expects($this->once())
            ->method('setExpireQuotesAdditionalFilterFields')
            ->with(['is_persistent' => 0])
            ->will($this->returnSelf());

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getSalesObserver'], [], '', false);
        $eventMock->expects($this->once())->method('getSalesObserver')->will($this->returnValue($salesObserverMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->subject->execute($observerMock);
    }
}
 