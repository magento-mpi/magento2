<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCard\Block\Product\View;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Covered afterGetWishlistOptions
     *
     * @test
     */
    public function testAfterGetWishlistOptions()
    {
        $expected = ['key1' => 'value1', 'giftcardInfo' => '[id^=giftcard]'];
        $param = ['key1' => 'value1'];
        $block = $this->getMockBuilder('Magento\Catalog\Block\Product\View')->disableOriginalConstructor()->getMock();
        /** @var $block \Magento\Catalog\Block\Product\View */
        $this->assertEquals($expected, (new Plugin())->afterGetWishlistOptions($block, $param));
    }
}
