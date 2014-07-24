<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Catalog\Product\Price;

class GiftcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $amounts
     * @param bool $withCustomOptions
     * @param float $expectedPrice
     * @dataProvider testGetPriceDataProvider
     */
    public function testGetPrice($amounts, $withCustomOptions, $expectedPrice)
    {
        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getData', 'getAllowOpenAmount', 'hasCustomOptions', '__wakeup'),
            array(),
            '',
            false
        );
        $product->expects($this->once())->method('getAllowOpenAmount')->will($this->returnValue(false));
        $product->expects($this->any())->method('hasCustomOptions')->will($this->returnValue($withCustomOptions));
        $product->expects($this->atLeastOnce())->method('getData')->will(
            $this->returnValueMap([['price', null, null], ['giftcard_amounts', null, $amounts]])
        );

        $giftCard = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject('Magento\GiftCard\Model\Catalog\Product\Price\Giftcard');
        $this->assertEquals($expectedPrice, $giftCard->getPrice($product));
    }

    /**
     * @return array
     */
    public function testGetPriceDataProvider()
    {
        return [
            [[['website_id' => 0, 'value' => '10.0000', 'website_value' => 10]], false, 10],
            [[['website_id' => 0, 'value' => '10.0000', 'website_value' => 10]], true, 0],
            [
                [
                    ['website_id' => 0, 'value' => '10.0000', 'website_value' => 10],
                    ['website_id' => 0, 'value' => '100.0000', 'website_value' => 100]
                ],
                false,
                0
            ],
        ];
    }
}
