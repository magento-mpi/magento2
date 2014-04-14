<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

/**
 * Class RegularPriceTest
 */
class RegularPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Price\RegularPrice
     */
    protected $regularPrice;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Pricing\Amount\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator
     */
    protected $calculatorMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $qty = 1;
        $this->salableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->priceInfoMock = $this->getMock('Magento\Pricing\PriceInfo\Base', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Pricing\Amount', [], [], '', false);
        $this->calculatorMock = $this->getMock('Magento\Pricing\Adjustment\Calculator', [], [], '', false);

        $this->salableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $this->regularPrice = new RegularPrice($this->salableItemMock, $qty, $this->calculatorMock);
    }

    /**
     * Test method testGetValue
     *
     * @param float|bool $price
     * @dataProvider testGetValueDataProvider
     */
    public function testGetValue($price)
    {
        $this->salableItemMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($price));
        $this->assertEquals($price, $this->regularPrice->getValue());
    }

    /**
     * Data provider for testGetValue
     *
     * @return array
     */
    public function testGetValueDataProvider()
    {
        return [
            'With price' => [100.00],
            'Without price' => [false]
        ];
    }

    /**
     * Test method testGetDisplayValue
     */
    public function testGetAmount()
    {
        $priceValue = 77;
        $amountValue = 88;
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo($priceValue))
            ->will($this->returnValue($amountValue));
        $this->salableItemMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($priceValue));

        $this->assertEquals($amountValue, $this->regularPrice->getAmount());
    }

    /**
     * Test method getPriceType
     */
    public function testGetPriceType()
    {
        $this->assertEquals(RegularPrice::PRICE_TYPE_CODE, $this->regularPrice->getPriceType());
    }
}
