<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

class SkipWebsiteRestrictionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentSessionMock;

    /**
     * @var \Magento\PersistentHistory\Model\Observer\SkipWebsiteRestriction
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->persistentSessionMock = $this->getMock('\Magento\Persistent\Helper\Session', [], [], '', false);
        $this->subject = $objectManager->getObject(
            '\Magento\PersistentHistory\Model\Observer\SkipWebsiteRestriction',
            ['persistentSession' => $this->persistentSessionMock]
        );
    }

    public function testSkipWebsiteRestrictionIfResultCantProcess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $resultMock = $this->getMock('\Magento\Framework\Object', ['getShouldProceed'], [], '', false);

        $resultMock->expects($this->once())
            ->method('getShouldProceed')
            ->will($this->returnValue(false));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getResult'], [], '', false);
        $eventMock->expects($this->once())->method('getResult')->will($this->returnValue($resultMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->subject->execute($observerMock);
    }

    public function testSkipWebsiteRestrictionIfSessionNotPersistent()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $resultMock = $this->getMock('\Magento\Framework\Object', ['getShouldProceed'], [], '', false);

        $resultMock->expects($this->once())
            ->method('getShouldProceed')
            ->will($this->returnValue(true));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getResult'], [], '', false);
        $eventMock->expects($this->once())->method('getResult')->will($this->returnValue($resultMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(false));

        $this->subject->execute($observerMock);
    }

    public function testSkipWebsiteRestrictionSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $resultMock = $this->getMock(
            '\Magento\Framework\Object',
            ['getShouldProceed', 'setCustomerLoggedIn'],
            [],
            '',
            false
        );

        $resultMock->expects($this->once())->method('getShouldProceed')->will($this->returnValue(true));
        $resultMock->expects($this->once())
            ->method('setCustomerLoggedIn')
            ->with(true)
            ->will($this->returnSelf());

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getResult'], [], '', false);
        $eventMock->expects($this->once())->method('getResult')->will($this->returnValue($resultMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));

        $this->subject->execute($observerMock);
    }
}
 