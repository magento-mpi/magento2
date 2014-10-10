<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data\Product;

/**
 * Interface TierPrice must be implemented Magento\Catalog\Model\Product\Type\Price\Tier  model
 * @see \Magento\Catalog\Service\V1\Data\Product\TierPrice
 */
interface TierPriceInterface
{
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
