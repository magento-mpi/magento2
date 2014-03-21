<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product;

/**
 * MSRP price model
 */
interface TierPriceInterface
{
    /**
     * @return array
     */
    public function getApplicableTierPrices();
}
