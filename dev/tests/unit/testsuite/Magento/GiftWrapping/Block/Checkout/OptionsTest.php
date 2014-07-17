<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Block\Checkout;

use Magento\TestFramework\Helper\ObjectManager;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\GiftWrapping\Block\Checkout\Options
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $checkoutItems = [
            'onepage' => [
                'order_level' => 'quote',
                'item_level' => 'quote_item',
            ],
            'multishipping' => [
                'order_level' => 'quote_address',
                'item_level' => 'quote_address_item',
            ],
        ];
        $this->quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['getIsMultiShipping', '__wakeup'],
            [],
            '',
            false
        );

        $checkoutSessionMock = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false);
        $checkoutSessionMock->expects($this->any())->method('getQuote')->will($this->returnValue($this->quoteMock));


        $this->block = $this->objectManager->getObject(
            '\Magento\GiftWrapping\Block\Checkout\Options',
            [
                'checkoutSession' => $checkoutSessionMock,
                'checkoutItems' => $checkoutItems,
            ]
        );
    }

    /**
     * @dataProvider getCheckoutTypeVariableProvider
     * @param bool $isMultiShipping
     * @param string $level
     * @param string $expectedResult
     */
    public function testGetCheckoutTypeVariable($isMultiShipping, $level, $expectedResult)
    {
        $this->quoteMock->expects($this->once())
            ->method('getIsMultiShipping')
            ->will($this->returnValue($isMultiShipping));

        $this->assertEquals($expectedResult, $this->block->getCheckoutTypeVariable($level));
    }

    public function getCheckoutTypeVariableProvider()
    {
        return [
            'onepage_order_level' => [false, 'order_level', 'quote'],
            'onepage_item_level' => [false, 'item_level', 'quote_item'],
            'multishipping_order_level' => [true, 'order_level', 'quote_address'],
            'multishipping_item_level' => [true, 'item_level', 'quote_address_item'],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetCheckoutTypeVariableException()
    {
        $this->quoteMock->expects($this->once())->method('getIsMultiShipping')->will($this->returnValue(false));
        $this->block->getCheckoutTypeVariable('wrong_level');
    }
}