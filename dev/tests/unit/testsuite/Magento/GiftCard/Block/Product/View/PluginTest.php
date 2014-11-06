<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->assertEquals($expected, (new Plugin())->afterGetWishlistOptions(null, $param));
    }
}
