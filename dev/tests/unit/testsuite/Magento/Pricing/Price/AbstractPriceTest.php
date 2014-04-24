<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\Price;

/**
 * Class RegularPriceTest
 */
class AbstractPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractPrice
     */
    protected $price;

    /**
     * @var \Magento\Framework\Pricing\PriceInfoInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Framework\Pricing\Amount\Base |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $amountMock;

    /**
     * @var \Magento\Framework\Pricing\Object\SaleableInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Framework\Pricing\Adjustment\Calculator |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $qty = 1;
        $this->saleableItemMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->priceInfoMock = $this->getMock('Magento\Framework\Pricing\PriceInfo\Base', [], [], '', false);
        $this->amountMock = $this->getMock('Magento\Framework\Pricing\Amount', [], [], '', false);
        $this->calculatorMock = $this->getMock('Magento\Framework\Pricing\Adjustment\Calculator', [], [], '', false);

        $this->saleableItemMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->price = $objectManager->getObject(
            'Magento\Framework\Pricing\Price\Stub',
            [
                'saleableItem' => $this->saleableItemMock,
                'quantity' => $qty,
                'calculator' => $this->calculatorMock
            ]
        );
    }

    /**
     * Test method testGetDisplayValue
     */
    public function testGetAmount()
    {
        $priceValue = $this->price->getValue();
        $amountValue = 88;
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($this->equalTo($priceValue))
            ->will($this->returnValue($amountValue));
        $this->assertEquals($amountValue, $this->price->getAmount());
    }

    /**
     * Test method getPriceType
     */
    public function testGetPriceCode()
    {
        $this->assertEquals(AbstractPrice::PRICE_CODE, $this->price->getPriceCode());
    }

    public function testGetCustomAmount()
    {
        $exclude = false;
        $amount = 21.0;
        $customAmount = 42.0;
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($amount, $this->saleableItemMock, $exclude)
            ->will($this->returnValue($customAmount));

        $this->assertEquals($customAmount, $this->price->getCustomAmount($amount, $exclude));
    }

    public function testGetCustomAmountDefault()
    {
        $customAmount = 42.0;
        $this->calculatorMock->expects($this->once())
            ->method('getAmount')
            ->with($this->price->getValue(), $this->saleableItemMock, null)
            ->will($this->returnValue($customAmount));

        $this->assertEquals($customAmount, $this->price->getCustomAmount());
    }
}
