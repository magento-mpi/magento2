<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Validator;

/**
 * Test Class DiscountTest
 * @package Magento\GiftCard\Model\Validator
 */
class DiscountTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValid()
    {
        $discount = new Discount();
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', [], [], '', false);
        $item->expects($this->at(0))
            ->method('getProductType')
            ->willReturn(\Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD);

        $item->expects($this->at(1))
            ->method('getProductType')
            ->willReturn($this->anything());

        $this->assertFalse($discount->isValid($item));
        $this->assertTrue($discount->isValid($item));

        $this->assertEmpty($discount->getMessages());
    }
}
 
