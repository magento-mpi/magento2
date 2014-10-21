<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * Interface TierPrice must be implemented @see \Magento\Catalog\Pricing\Price\TierPrice  model
 * @see \Magento\Catalog\Service\V1\Data\Product\TierPrice
 * @todo remove this interface if framework support return array
 */
interface ProductTierPriceInterface
{
    const QTY = 'qty';

    const VALUE = 'value';

    /**
     * Retrieve tier qty
     *
     * @return float
     */
    public function getQty();

    /**
     * Retrieve price value
     *
     * @return float
     */
    public function getValue();
}
