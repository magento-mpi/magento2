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
     * @var \Magento\Customer\Api\AccountManagementInterface| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accountManagementMock;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepositoryMock;

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
        $this->currentCustomerMock = $this->getMock(
            'Magento\Customer\Helper\Session\CurrentCustomer',
            array(),
            array(),
            '',
            false
        );
        $this->customerRepositoryMock = $this->getMock(
            'Magento\Customer\Api\CustomerRepositoryInterface',
            array(),
            array(),
            '',
            false
        );

        $this->accountManagementMock = $this->getMock(
            'Magento\Customer\Api\AccountManagementInterface',
            array(),
            array(),
            '',
            false
        );
        $this->customerAddressDataMock = $this->getMock(
            'Magento\Customer\Api\Data\AddressInterface',
            array(),
            array(),
            '',
            false
        );

        $this->currentCustomerAddress = new \Magento\Customer\Helper\Session\CurrentCustomerAddress(
            $this->currentCustomerMock,
            $this->accountManagementMock,
            $this->customerRepositoryMock
        );
    }

    /**
     * Test getCustomerAddresses
     */
    public function testGetCustomerAddresses()
    {
        $customerDataMock = $this->getMock(
            'Magento\Customer\Api\Data\CustomerInterface',
            [],
            [],
            '',
            false
        );


        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->customerCurrentId)
            ->will($this->returnValue($customerDataMock));

        $customerDataMock->expects($this->once())
            ->method('getAddresses')
            ->will($this->returnValue(array($this->customerAddressDataMock)));
        $this->assertEquals(
            array($this->customerAddressDataMock),
            $this->currentCustomerAddress->getCustomerAddresses()
        );
    }

    /**
     * test getDefaultBillingAddress
     */
    public function testGetDefaultBillingAddress()
    {
        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));
        $this->accountManagementMock->expects($this->once())
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
        $this->accountManagementMock->expects($this->once())
            ->method('getDefaultShippingAddress')
            ->will($this->returnValue($this->customerAddressDataMock));
        $this->assertEquals(
            $this->customerAddressDataMock,
            $this->currentCustomerAddress->getDefaultShippingAddress()
        );
    }
}
