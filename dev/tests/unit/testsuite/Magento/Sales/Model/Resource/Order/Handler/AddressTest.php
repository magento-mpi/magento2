<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Model\Resource\Order\Handler;

/**
 * Class AddressTest
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Handler\Address
     */
    protected $address;
    /**
     * @var \Magento\Sales\Model\Resource\Order\Address\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressCollectionMock;
    /**
     * @var \Magento\Sales\Model\Resource\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;
    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;
    /**
     * @var \Magento\Sales\Model\Order\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressMock;

    public function setUp()
    {
        $this->attributeMock = $this->getMock(
            'Magento\Sales\Model\Resource\Attribute',
            [],
            [],
            '',
            false
        );
        $this->orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            [
                '__wakeup',
                'getAddresses',
                'save',
                'getBillingAddress',
                'getShippingAddress',
                'hasBillingAddressId',
                'getBillingAddressId',
                'setBillingAddressId',
                'unsBillingAddressId',
                'hasShippingAddressId',
                'getShippingAddressId',
                'setShippingAddressId',
                'unsShippingAddressId'
            ],
            [],
            '',
            false
        );
        $this->addressMock = $this->getMock(
            'Magento\Sales\Model\Order\Address',
            [],
            [],
            '',
            false
        );
        $this->addressCollectionMock = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Address\Collection',
            [],
            [],
            '',
            false
        );
        $this->address = new \Magento\Sales\Model\Resource\Order\Handler\Address(
            $this->attributeMock
        );
    }

    /**
     * Test process method with billing_address
     */
    public function testProcessBillingAddress()
    {
        $this->orderMock->expects($this->exactly(2))
            ->method('getAddresses')
            ->willReturn([$this->addressCollectionMock]);
        $this->addressCollectionMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->orderMock->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue($this->addressMock));
        $this->addressMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(2));
        $this->orderMock->expects($this->once())
            ->method('getBillingAddressId')
            ->will($this->returnValue(1));
        $this->orderMock->expects($this->once())
            ->method('setBillingAddressId')
            ->will($this->returnSelf());
        $this->orderMock->expects($this->once())
            ->method('getShippingAddress')
            ->will($this->returnValue(null));
        $this->attributeMock->expects($this->once())
            ->method('saveAttribute')
            ->with($this->orderMock, ['billing_address_id'])
            ->will($this->returnSelf());
        $this->assertEquals($this->address, $this->address->process($this->orderMock));
    }

    /**
     * Test process method with shipping_address
     */
    public function testProcessShippingAddress()
    {
        $this->orderMock->expects($this->exactly(2))
            ->method('getAddresses')
            ->willReturn([$this->addressCollectionMock]);
        $this->addressCollectionMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->orderMock->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue(null));
        $this->orderMock->expects($this->once())
            ->method('getShippingAddress')
            ->will($this->returnValue($this->addressMock));
        $this->addressMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(2));
        $this->orderMock->expects($this->once())
            ->method('setShippingAddressId')
            ->will($this->returnSelf());
        $this->attributeMock->expects($this->once())
            ->method('saveAttribute')
            ->with($this->orderMock, ['shipping_address_id'])
            ->will($this->returnSelf());
        $this->assertEquals($this->address, $this->address->process($this->orderMock));
    }

    /**
     * Test method removeEmptyAddresses
     */
    public function testRemoveEmptyAddresses()
    {
        $this->orderMock->expects($this->once())
            ->method('hasBillingAddressId')
            ->will($this->returnValue(true));
        $this->orderMock->expects($this->once())
            ->method('getBillingAddressId')
            ->will($this->returnValue(null));
        $this->orderMock->expects($this->once())
            ->method('unsBillingAddressId')
            ->will($this->returnSelf());
        $this->orderMock->expects($this->once())
            ->method('hasShippingAddressId')
            ->will($this->returnValue(true));
        $this->orderMock->expects($this->once())
            ->method('getShippingAddressId')
            ->will($this->returnValue(null));
        $this->orderMock->expects($this->once())
            ->method('unsShippingAddressId')
            ->will($this->returnSelf());
        $this->assertEquals($this->address, $this->address->removeEmptyAddresses($this->orderMock));
    }
}
