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
 * Interface TierPrice must be implemented Magento\Catalog\Model\Product\Type\Price  model
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
