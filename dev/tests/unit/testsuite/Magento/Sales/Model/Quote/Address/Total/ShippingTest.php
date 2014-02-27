<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Address\Total;

class ShippingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Quote\Address\Total\Shipping
     */
    protected $shippingModel;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->shippingModel = $objectManager->getObject('Magento\Sales\Model\Quote\Address\Total\Shipping');
    }

    /**
     * @dataProvider fetchDataProvider
     */
    public function testFetch($shippingAmount, $shippingDescription)
    {
        $address = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            ['getShippingAmount', 'getShippingDescription', 'addTotal', '__wakeup'],
            [],
            '',
            false
        );

        $address->expects($this->once())
            ->method('getShippingAmount')
            ->will($this->returnValue($shippingAmount));

        $address->expects($this->once())
            ->method('getShippingDescription')
            ->will($this->returnValue($shippingDescription));

        $address->expects($this->once())
            ->method('addTotal')
            ->will($this->returnSelf());

        $this->assertEquals($this->shippingModel, $this->shippingModel->fetch($address));
    }

    public function fetchDataProvider()
    {
        return [
            [
                'shipping_amount' => 1,
                'shipping_description' => 'Shipping Method'
            ],
            [
                'shipping_amount' => 1,
                'shipping_description' => ''
            ],
        ];
    }
}
