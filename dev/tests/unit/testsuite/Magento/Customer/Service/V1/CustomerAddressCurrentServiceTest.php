<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;


class CustomerAddressCurrentServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressCurrentService
     */
    protected $customerAddressCurrentService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerCurrentServiceMock;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAddressServiceMock;

    /**
     * @var \Magento\Customer\Service\V1\Dto\Address
     */
    protected $customerAddressDtoMock;

    /**
     * @var int
     */
    protected $customerCurrentId = 100;

    /**
     * Test setup
     */
    public function setUp()
    {
        $this->customerCurrentServiceMock = $this->getMock('Magento\Customer\Service\V1\CustomerCurrentService',
            array(), array(), '', false);
        $this->customerAddressServiceMock = $this->getMock('Magento\Customer\Service\V1\CustomerAddressService',
            array(), array(), '', false);

        $this->customerAddressCurrentService = new \Magento\Customer\Service\V1\CustomerAddressCurrentService(
            $this->customerCurrentServiceMock,
            $this->customerAddressServiceMock
        );
    }

    /**
     * Test getCustomerAddresses
     */
    public function testGetCustomerAddresses()
    {
        $this->customerCurrentServiceMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));
        $this->customerAddressServiceMock->expects($this->once())
            ->method('getAddresses')
            ->will($this->returnValue(array($this->customerAddressDtoMock)));
        $this->assertEquals(array($this->customerAddressDtoMock),
            $this->customerAddressCurrentService->getCustomerAddresses());
    }

    /**
     * test getDefaultBillingAddress
     */
    public function testGetDefaultBillingAddress()
    {
        $this->customerCurrentServiceMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));
        $this->customerAddressServiceMock->expects($this->once())
            ->method('getDefaultBillingAddress')
            ->will($this->returnValue($this->customerAddressDtoMock));
        $this->assertEquals($this->customerAddressDtoMock,
            $this->customerAddressCurrentService->getDefaultBillingAddress());
    }

    /**
     * test getDefaultShippingAddress
     */
    public function testGetDefaultShippingAddress()
    {
        $this->customerCurrentServiceMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerCurrentId));
        $this->customerAddressServiceMock->expects($this->once())
            ->method('getDefaultShippingAddress')
            ->will($this->returnValue($this->customerAddressDtoMock));
        $this->assertEquals($this->customerAddressDtoMock,
            $this->customerAddressCurrentService->getDefaultShippingAddress());
    }
}
