<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCard\Block\Product\View;

use Magento\Catalog\Block\Product\View;

/**
 * Plugin for Magento\Catalog\Block\Product\View
 */
class Plugin
{
    /**
     * Return wishlist widget options
     *
     * @param View $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetWishlistOptions(View $subject, $result)
    {
        return array_merge($result, ['giftcardInfo' => '[id^=giftcard]']);
    }
}
