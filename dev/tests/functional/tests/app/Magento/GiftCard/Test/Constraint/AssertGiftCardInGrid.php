<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductInGrid;

/**
 * Assert that gift card product is present in products grid.
 */
class AssertGiftCardInGrid extends AssertProductInGrid
{
    /**
     * Gift Card product type
     */
    const GIFT_CARD = 'Gift Card';

    /**
     * Get product type
     *
     * @return string
     */
    protected function getProductType()
    {
        return self::GIFT_CARD;
    }
}
