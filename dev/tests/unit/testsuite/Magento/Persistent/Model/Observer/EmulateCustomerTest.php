<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Persistent\Model\Observer;

class EmulateCustomerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Persistent\Model\Observer\EmulateCustomer
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAccountMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    protected function setUp()
    {
        $this->customerAccountMock = $this->getMock('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->sessionHelperMock = $this->getMock('Magento\Persistent\Helper\Session', [], [], '', false);
        $this->helperMock = $this->getMock('Magento\Persistent\Helper\Data', [], [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->model = new \Magento\Persistent\Model\Observer\EmulateCustomer(
            $this->sessionHelperMock,
            $this->helperMock,
            $this->customerSessionMock,
            $this->customerAccountMock
        );
    }

    public function testExecuteWhenCannotProcessPersistentData()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('canProcess')
            ->with($this->observerMock)
            ->will($this->returnValue(false));
        $this->helperMock->expects($this->never())->method('isShoppingCartPersist');
        $this->sessionHelperMock->expects($this->never())->method('isPersistent');
        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenShoppingCartNotPersist()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('canProcess')
            ->with($this->observerMock)
            ->will($this->returnValue(true));
        $this->helperMock->expects($this->once())->method('isShoppingCartPersist')->will($this->returnValue(false));
        $this->sessionHelperMock->expects($this->never())->method('isPersistent');
        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenSessionPersistAndCustomerNotLoggedIn()
    {
        $customerId = 1;
        $customerGroupId = 2;
        $sessionMock = $this->getMock('Magento\Persistent\Model\Session', ['getCustomerId', '__wakeUp'], [], '', false);
        $customerMock = $this->getMock('\Magento\Customer\Service\V1\Data\Customer', [], [], '', false);
        $this->helperMock
            ->expects($this->once())
            ->method('canProcess')
            ->with($this->observerMock)
            ->will($this->returnValue(true));
        $this->helperMock->expects($this->once())->method('isShoppingCartPersist')->will($this->returnValue(true));
        $this->sessionHelperMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));
        $this->sessionHelperMock->expects($this->once())->method('getSession')->will($this->returnValue($sessionMock));
        $sessionMock->expects($this->once())->method('getCustomerId')->will($this->returnValue($customerId));
        $this->customerAccountMock
            ->expects($this->once())
            ->method('getCustomer')
            ->with(1)
            ->will($this->returnValue($customerMock));
        $customerMock->expects($this->once())->method('getId')->will($this->returnValue($customerId));
        $customerMock->expects($this->once())->method('getGroupId')->will($this->returnValue($customerGroupId));
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->will($this->returnSelf());
        $this->customerSessionMock->expects($this->once())->method('setCustomerGroupId')->with($customerGroupId);
        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenSessionNotPersist()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('canProcess')
            ->with($this->observerMock)
            ->will($this->returnValue(true));
        $this->helperMock->expects($this->once())->method('isShoppingCartPersist')->will($this->returnValue(true));
        $this->sessionHelperMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->customerAccountMock
            ->expects($this->never())
            ->method('getCustomer');
        $this->model->execute($this->observerMock);
    }
}
