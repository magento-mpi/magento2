<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Sales\Order;

use Magento\TestFramework\Helper\ObjectManager;

class TaxManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaxManagement
     */
    private $taxManagement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $taxItemResourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $taxItemFactoryMock;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderFactoryMock;

    public function setUp()
    {
        $this->orderMock = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $this->orderFactoryMock = $this->getMock('Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        $this->taxItemResourceMock = $this->getMock(
            'Magento\Tax\Model\Resource\Sales\Order\Tax\Item',
            [],
            [],
            '',
            false
        );
        $this->taxItemFactoryMock = $this->getMock(
            'Magento\Tax\Model\Resource\Sales\Order\Tax\ItemFactory',
            ['create'],
            [],
            '',
            false
        );

        $objectManager = new ObjectManager($this);
        $this->taxManagement = $objectManager->getObject(
            'Magento\Tax\Service\V1\OrderTaxService',
            [
                'orderFactory' => $this->orderFactoryMock,
                'orderItemTaxFactory' => $this->taxItemFactoryMock,
            ]
        );
    }

    public function testGetOrderTaxDetails()
    {
        $orderId = 1;
        $this->orderFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->orderMock));
        $this->orderMock->expects($this->once())
            ->method('load')
            ->with($orderId)
            ->will($this->returnSelf());
        // @todo implement this test
    }
}
