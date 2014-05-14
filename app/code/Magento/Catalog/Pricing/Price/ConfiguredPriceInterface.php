<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Configured price interface
 */
interface ConfiguredPriceInterface
{
    /**
     * Price type configured
     */
    const CONFIGURED_PRICE_CODE = 'configured_price';

    /**
     * @param ItemInterface $item
     * @return $this
     */
    public function setItem(ItemInterface $item);
}
