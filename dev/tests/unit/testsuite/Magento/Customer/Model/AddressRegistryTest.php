<?php
/**
 * Unit test for converter \Magento\Customer\Model\Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

class AddressRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    private $unit;

    /**
     * @var \Magento\Customer\Model\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $addressFactory;

    public function setUp()
    {
        $this->addressFactory = $this->getMockBuilder('\Magento\Customer\Model\AddressFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->unit = new AddressRegistry($this->addressFactory);
    }

    public function testRetrieve()
    {
        $addressId = 1;
        $address = $this->getMockBuilder('\Magento\Customer\Model\AddressFactory')
            ->disableOriginalConstructor()
            ->setMethods(['load', 'getId'])
            ->getMock();
        $address->expects($this->once())
            ->method('load')
            ->with($addressId)
            ->will($this->returnValue($address));
        $address->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($addressId));
        $this->addressFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($address));
        $actual = $this->unit->retrieve($addressId);
        $this->assertEquals($address, $actual);
        $actualCached = $this->unit->retrieve($addressId);
        $this->assertEquals($address, $actualCached);
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $addressId = 1;
        $address = $this->getMockBuilder('\Magento\Customer\Model\AddressFactory')
            ->setMethods(['load', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $address->expects($this->once())
            ->method('load')
            ->with($addressId)
            ->will($this->returnValue($address));
        $address->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->addressFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($address));
        $actual = $this->unit->retrieve($addressId);
    }

    public function testRemove()
    {
        $addressId = 1;
        $address = $this->getMockBuilder('\Magento\Customer\Model\AddressFactory')
            ->disableOriginalConstructor()
            ->setMethods(['load', 'getId'])
            ->getMock();
        $address->expects($this->exactly(2))
            ->method('load')
            ->with($addressId)
            ->will($this->returnValue($address));
        $address->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue($addressId));
        $this->addressFactory->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($address));
        $actual = $this->unit->retrieve($addressId);
        $this->assertEquals($address, $actual);
        $this->unit->remove($addressId);
        $actual = $this->unit->retrieve($addressId);
        $this->assertEquals($address, $actual);
    }
}