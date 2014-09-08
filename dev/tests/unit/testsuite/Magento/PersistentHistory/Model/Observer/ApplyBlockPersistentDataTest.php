<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

class ApplyBlockPersistentDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \Magento\PersistentHistory\Model\Observer\ApplyBlockPersistentData
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->persistentHelperMock = $this->getMock('\Magento\PersistentHistory\Helper\Data', [], [], '', false);
        $this->observerMock = $this->getMock(
            '\Magento\Persistent\Model\Observer\ApplyBlockPersistentData',
            [],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            '\Magento\PersistentHistory\Model\Observer\ApplyBlockPersistentData',
            [
                'ePersistentData' => $this->persistentHelperMock,
                'observer' => $this->observerMock,
            ]
        );
    }

    public function testApplyBlockPersistentData()
    {
        $configFilePath = 'file/path';
        $eventObserverMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['setConfigFilePath'], [], '', false);
        $eventMock->expects($this->once())
            ->method('setConfigFilePath')
            ->with($configFilePath)
            ->will($this->returnSelf());

        $eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->persistentHelperMock->expects($this->once())
            ->method('getPersistentConfigFilePath')
            ->will($this->returnValue($configFilePath));

        $this->observerMock->expects($this->once())
            ->method('execute')
            ->with($eventObserverMock)
            ->will($this->returnSelf());

        $this->assertEquals($this->observerMock, $this->subject->execute($eventObserverMock));
    }
}
 