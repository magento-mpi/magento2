<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pci\Model\Encryption|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $encryptorMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    protected function setUp()
    {
        $this->encryptorMock = $this->getMockBuilder('\Magento\Pci\Model\Encryption')
            ->disableOriginalConstructor()
            ->getMock();
        $this->encryptorMock->expects($this->any())
            ->method('validateHashByVersion')
            ->will(
                $this->returnCallback(
                    function ($arg1, $arg2) {
                        return $arg1 == $arg2;
                    }
                )
            );
        $this->customerMock = $this->getMockBuilder('\Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->setMethods(array('getPasswordHash', 'changePassword', '__wakeup'))
            ->getMock();
    }

    protected function getObserverMock($password, $passwordHash)
    {
        $this->customerMock->expects($this->once())
            ->method('getPasswordHash')
            ->will($this->returnValue($passwordHash));

        $event = new \Magento\Object();
        $event->setData(
            array(
                'password' => $password,
                'model' => $this->customerMock
            )
        );

        $observerMock = new \Magento\Object();
        $observerMock->setData('event', $event);

        return $observerMock;
    }

    public function testUpgradeCustomerPassword()
    {
        $this->customerMock->expects($this->once())
            ->method('changePassword')
            ->will($this->returnSelf());
        $observer = new \Magento\Pci\Model\Observer($this->encryptorMock);
        $observer->upgradeCustomerPassword($this->getObserverMock('different password', 'old password'));
    }

    public function testUpgradeCustomerPasswordNotChanged()
    {
        $this->customerMock->expects($this->never())
            ->method('changePassword');
        $observer = new \Magento\Pci\Model\Observer($this->encryptorMock);
        $observer->upgradeCustomerPassword($this->getObserverMock('same password', 'same password'));
    }
}