<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

class ApplyPersistentDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ePersistentDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mPersistentDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configFactoryMock;

    /**
     * @var \Magento\PersistentHistory\Model\Observer\ApplyPersistentData
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->ePersistentDataMock = $this->getMock('\Magento\PersistentHistory\Helper\Data', [], [], '', false);
        $this->persistentSessionMock = $this->getMock('\Magento\Persistent\Helper\Session', [], [], '', false);
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->configFactoryMock = $this->getMock(
            '\Magento\Persistent\Model\Persistent\ConfigFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->mPersistentDataMock = $this->getMock(
            '\Magento\Persistent\Helper\Data',
            ['isCompareProductsPersist', 'canProcess', '__wakeup'],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            '\Magento\PersistentHistory\Model\Observer\ApplyPersistentData',
            [
                'ePersistentData' => $this->ePersistentDataMock,
                'persistentSession' => $this->persistentSessionMock,
                'mPersistentData' => $this->mPersistentDataMock,
                'customerSession' => $this->customerSessionMock,
                'configFactory' => $this->configFactoryMock
            ]
        );
    }

    public function testApplyPersistentDataIfDataCantProcess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataIfSessionNotPersistent()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataIfUserLoggedIn()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataSuccess()
    {
        $configFilePath = 'file/path';
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));

        $configMock = $this->getMock('\Magento\Persistent\Model\Persistent\Config', [], [], '', false);
        $configMock->expects($this->once())
            ->method('setConfigFilePath')
            ->with($configFilePath)
            ->will($this->returnSelf());

        $this->ePersistentDataMock->expects($this->once())
            ->method('getPersistentConfigFilePath')
            ->will($this->returnValue($configFilePath));

        $this->configFactoryMock->expects($this->once())->method('create')->will($this->returnValue($configMock));
        $this->subject->execute($observerMock);
    }
}
 