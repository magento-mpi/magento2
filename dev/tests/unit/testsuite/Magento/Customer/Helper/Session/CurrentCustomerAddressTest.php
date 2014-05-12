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
     * @var \Magento\Customer\Service\V1\CustomerAddressService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAddressServiceMock;

    /**
     * @var \Magento\Customer\Service\V1\Data\Address
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
        $this->customerAddressServiceMock = $this->getMock(
            'Magento\Customer\Service\V1\CustomerAddressService',
            array(),
            array(),
            '',
            false
        );

        $this->currentCustomerAddress = new \Magento\Customer\Helper\Session\CurrentCustomerAddress(
            $this->currentCustomerMock,
            $this->customerAddressServiceMock
        );
    }

    /**
     * Test getCustomerAddresses
     */
    public function testGetCustomerAddresses()
    {
        $this->currentCustomerMock->expects(
            $this->once()
        )->method(
                'getCustomerId'
            )->will(
                $this->returnValue($this->customerCurrentId)
            );
        $this->customerAddressServiceMock->expects(
            $this->once()
        )->method(
                'getAddresses'
            )->will(
                $this->returnValue(array($this->customerAddressDataMock))
            );
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
        $this->currentCustomerMock->expects(
            $this->once()
        )->method(
                'getCustomerId'
            )->will(
                $this->returnValue($this->customerCurrentId)
            );
        $this->customerAddressServiceMock->expects(
            $this->once()
        )->method(
                'getDefaultBillingAddress'
            )->will(
                $this->returnValue($this->customerAddressDataMock)
            );
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
        $this->currentCustomerMock->expects(
            $this->once()
        )->method(
                'getCustomerId'
            )->will(
                $this->returnValue($this->customerCurrentId)
            );
        $this->customerAddressServiceMock->expects(
            $this->once()
        )->method(
                'getDefaultShippingAddress'
            )->will(
                $this->returnValue($this->customerAddressDataMock)
            );
        $this->assertEquals(
            $this->customerAddressDataMock,
            $this->currentCustomerAddress->getDefaultShippingAddress()
        );
    }
}
