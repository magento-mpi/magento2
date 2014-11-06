<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Product\View;

/**
 * Plugin for Magento\Catalog\Block\Product\View
 */
class Plugin
{
    /**
     * Return wishlist widget options
     *
     * @param array $result
     * @return array
     */
    public function afterGetWishlistOptions($object, $result)
    {
        return array_merge($result, ['giftcardInfo' => '[id^=giftcard]']);
    }
}
