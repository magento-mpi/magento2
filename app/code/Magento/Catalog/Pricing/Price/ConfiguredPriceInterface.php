<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
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
