<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class OrderAddressUpdateTest
 */
class OrderAddressUpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrderAddressUpdate
     */
    protected $orderAddressUpdate;

    /**
     * @var \Magento\Sales\Model\Order\AddressConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressConverterMock;

    protected function setUp()
    {
        $this->addressConverterMock = $this->getMock(
            'Magento\Sales\Model\Order\AddressConverter',
            ['getModel'],
            [],
            '',
            false
        );
        $this->orderAddressUpdate = new OrderAddressUpdate(
            $this->addressConverterMock
        );
    }

    /**
     * test Order Address Update service
     */
    public function testInvoke()
    {
        $dtoMock = $this->getMock(
            '\Magento\Sales\Service\V1\Data\OrderAddress',
            [],
            [],
            '',
            false
        );

        $orderAddressModel = $this->getMock(
            'Magento\Sales\Model\Order\Address',
            ['save', '__wakeup'],
            [],
            '',
            false
        );
        $this->addressConverterMock->expects($this->once())
            ->method('getModel')
            ->with($this->equalTo($dtoMock))
            ->will($this->returnValue($orderAddressModel));
        $orderAddressModel->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());
        $this->orderAddressUpdate->invoke($dtoMock);

    }
}
