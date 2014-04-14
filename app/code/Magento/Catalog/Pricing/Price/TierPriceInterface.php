<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

/**
 * Tier price interface
 */
interface TierPriceInterface
{
    /**
     * Price type tier
     */
    const PRICE_TYPE_CODE = 'tier_price';

    /**
     * @return array
     */
    public function getTierPriceList();

    /**
     * @return int
     */
    public function getTierPriceCount();
}
