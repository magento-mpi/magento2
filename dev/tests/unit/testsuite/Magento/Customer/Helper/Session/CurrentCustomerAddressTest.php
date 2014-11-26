<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper\Session;

class CurrentCustomerAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomerAddress
     */
    protected $currentCustomerAddress;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currentCustomerMock;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAccountManagementMock;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterface
     */
    protected $customerAddressDataMock;

    /**
     * @var int
     */
    protected $customerCurrentId = 100;

    /**
     * Test setup
     */
    public function setUp()
    {
        $this->currentCustomerMock = $this->getMockBuilder('Magento\Customer\Helper\Session\CurrentCustomer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerAccountManagementMock = $this->getMockBuilder('Magento\Customer\Api\AccountManagementInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerRepositoryMock = $this->getMockBuilder('Magento\Customer\Api\CustomerRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->currentCustomerAddress = new \Magento\Customer\Helper\Session\CurrentCustomerAddress(
            $this->currentCustomerMock,
            $this->customerAccountManagementMock,
            $this->customerRepositoryMock
        );
    }

    /**
     * Test getCustomerAddresses
     */
    public function testGetCustomerAddresses()
    {
        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));

        $customerMock = $this->getMockBuilder('Magento\Customer\Api\Data\CustomerInterface')
            ->setMethods(['getAddresses'])
            ->getMockForAbstractClass();
        $customerMock->expects($this->once())
            ->method('getAddresses')
            ->will($this->returnValue([$this->customerAddressDataMock]));

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->will($this->returnValue($customerMock));

        $this->assertEquals([$this->customerAddressDataMock], $this->currentCustomerAddress->getCustomerAddresses());
    }

    /**
     * test getDefaultBillingAddress
     */
    public function testGetDefaultBillingAddress()
    {
        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));

        $this->customerAccountManagementMock->expects($this->once())
            ->method('getDefaultBillingAddress')
            ->will($this->returnValue($this->customerAddressDataMock));
        $this->assertEquals(
            $this->customerAddressDataMock,
            $this->currentCustomerAddress->getDefaultBillingAddress()
        );
    }

    /**
     * test getDefaultShippingAddress
     */
    public function testGetDefaultShippingAddress()
    {
        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));
        $this->customerAccountManagementMock->expects($this->once())
            ->method('getDefaultShippingAddress')
            ->will($this->returnValue($this->customerAddressDataMock));
        $this->assertEquals(
            $this->customerAddressDataMock,
            $this->currentCustomerAddress->getDefaultShippingAddress()
        );
    }
}
