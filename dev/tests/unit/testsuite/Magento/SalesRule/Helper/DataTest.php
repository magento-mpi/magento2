<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Quote\Item
     */
    protected $quoteItemMock;

    /**
     * @var float
     */
    protected $basePrice;

    /**
     * @var float
     */
    protected $price;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objectManager->getObject('Magento\SalesRule\Helper\Data');
        $this->quoteItemMock = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            [
                'setDiscountCalculationPrice',
                'setBaseDiscountCalculationPrice',
                'getCalculationPrice',
                'getBaseCalculationPrice',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->basePrice = 10.5;
        $this->price = 20.5;
    }

    public function testSetItemDiscountPrices()
    {
        $this->quoteItemMock->expects($this->once())
            ->method('setDiscountCalculationPrice')
            ->with($this->price);
        $this->quoteItemMock->expects($this->once())
            ->method('setBaseDiscountCalculationPrice')
            ->with($this->basePrice);

        $this->assertEquals(
            $this->helper,
            $this->helper->setItemDiscountPrices($this->quoteItemMock, $this->basePrice, $this->price)
        );
    }

    public function testAddItemDiscountPrices()
    {
        $calculationPrice = 20.5;
        $baseCalculationPrice = 10.5;

        $this->quoteItemMock->expects($this->once())
            ->method('getCalculationPrice')
            ->will($this->returnValue($calculationPrice));
        $this->quoteItemMock->expects($this->once())
            ->method('getBaseCalculationPrice')
            ->will($this->returnValue($baseCalculationPrice));
        $this->quoteItemMock->expects($this->once())
            ->method('setDiscountCalculationPrice')
            ->with($calculationPrice + $this->price);
        $this->quoteItemMock->expects($this->once())
            ->method('setBaseDiscountCalculationPrice')
            ->with($baseCalculationPrice + $this->basePrice);

        $this->assertEquals(
            $this->helper,
            $this->helper->addItemDiscountPrices($this->quoteItemMock, $this->basePrice, $this->price)
        );
    }
} 