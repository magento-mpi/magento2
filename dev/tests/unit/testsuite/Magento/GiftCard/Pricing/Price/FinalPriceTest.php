<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Pricing\Price;

class FinalPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Pricing\Price\FinalPrice
     */
    protected $model;

    /**
     * @var \Magento\Framework\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\Catalog\Pricing\Price\BasePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $basePriceMock;

    /**
     * @var \Magento\Framework\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableMock;

    /**
     * @var \Magento\Framework\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;
    /**
     * @var \Magento\Catalog\Pricing\Price\SpecialPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * Set up function
     */
    public function setUp()
    {
        $this->saleableMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            [
                'getPriceInfo',
                'getGiftcardAmounts',
                '__wakeup'
            ],
            [],
            '',
            false
        );

        $this->priceInfoMock = $this->getMock(
            'Magento\Framework\Pricing\PriceInfo\Base',
            [],
            [],
            '',
            false
        );

        $this->basePriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\BasePrice',
            [],
            [],
            '',
            false
        );

        $this->priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($this->equalTo(\Magento\Catalog\Pricing\Price\BasePrice::PRICE_CODE))
            ->will($this->returnValue($this->basePriceMock));

        $this->calculatorMock = $this->getMock(
            'Magento\Framework\Pricing\Adjustment\Calculator',
            [],
            [],
            '',
            false
        );

        $this->saleableMock->expects($this->once())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->model = new \Magento\GiftCard\Pricing\Price\FinalPrice($this->saleableMock, 1, $this->calculatorMock);
    }

    /**
     * @param array $amounts
     * @param bool $expected
     *
     * @dataProvider getAmountsDataProvider
     */
    public function testGetAmounts($amounts, $expected)
    {
        $this->saleableMock->expects($this->any())
            ->method('getGiftcardAmounts')
            ->will($this->returnValue($amounts));

        $this->assertEquals($expected, $this->model->getAmounts());
    }

    /**
     * @return array
     */
    public function getAmountsDataProvider()
    {
        return [
            'one_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                ],
                'expected' => [10.],
            ],
            'two_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                    ['website_value' => 20.],
                ],
                'expected' => [10., 20.]
            ],
            'zero_amount' => [
                'amounts' => [],
                'expected' => [],
            ]

        ];
    }

    public function testGetAmountsCached()
    {
        $amount = [['website_value' => 5]];

        $this->saleableMock->expects($this->once())
            ->method('getGiftcardAmounts')
            ->will($this->returnValue($amount));

        $this->model->getAmounts();

        $this->assertEquals([5], $this->model->getAmounts());
    }

    /**
     * @param array $amounts
     * @param bool $expected
     *
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($amounts, $expected)
    {
        $this->saleableMock->expects($this->any())
            ->method('getGiftcardAmounts')
            ->will($this->returnValue($amounts));

        $this->assertEquals($expected, $this->model->getValue());
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return [
            'one_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                ],
                'expected' => 10.,
            ],
            'two_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                    ['website_value' => 20.],
                ],
                'expected' => 10.,
            ],
            'zero_amount' => [
                'amounts' => [],
                'expected' => false,
            ]

        ];
    }
}
