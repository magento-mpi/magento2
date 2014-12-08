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
     * @var \Magento\Framework\Encryption\Encryptor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $encryptorMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Magento\Pci\Model\Observer
     */
    protected $observer;

    protected function setUp()
    {
        $this->encryptorMock = $this->getMockBuilder(
            '\Magento\Framework\Encryption\Encryptor'
        )->disableOriginalConstructor()->getMock();
        $this->encryptorMock->expects($this->any())->method('validateHashByVersion')->will(
            $this->returnCallback(
                function ($arg1, $arg2) {
                    return $arg1 == $arg2;
                }
            )
        );
        $this->observer = new \Magento\Pci\Model\Observer($this->encryptorMock);
        $this->customerMock = $this->getMockBuilder(
            '\Magento\Customer\Model\Customer'
        )->disableOriginalConstructor()->setMethods(
            array('getPasswordHash', 'changePassword', '__wakeup')
        )->getMock();
    }

    /**
     * Create Observer with custom data structure and fill password
     *
     * @param $password
     * @param $passwordHash
     * @return \Magento\Framework\Object
     */
    protected function getObserverMock($password, $passwordHash)
    {
        $this->customerMock->expects(
            $this->once()
        )->method(
            'getPasswordHash'
        )->will(
            $this->returnValue($passwordHash)
        );

        $event = new \Magento\Framework\Object();
        $event->setData(array('password' => $password, 'model' => $this->customerMock));

        $observerMock = new \Magento\Framework\Object();
        $observerMock->setData('event', $event);

        return $observerMock;
    }

    /**
     * Test successfully password change if new password doesn't match old one
     */
    public function testUpgradeCustomerPassword()
    {
        $this->customerMock->expects($this->once())->method('changePassword')->will($this->returnSelf());
        $this->observer->upgradeCustomerPassword($this->getObserverMock('different password', 'old password'));
    }

    /**
     * Test failure password change if new password matches old one
     */
    public function testUpgradeCustomerPasswordNotChanged()
    {
        $this->customerMock->expects($this->never())->method('changePassword');
        $this->observer->upgradeCustomerPassword($this->getObserverMock('same password', 'same password'));
    }
}
