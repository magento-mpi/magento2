<?php
/**
 * Group Price
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * Interface GroupPrice must be implemented  @see \Magento\Catalog\Pricing\Price\GroupPrice  model
 * @see \Magento\Catalog\Service\V1\Data\Product\GroupPrice
 * @todo remove this interface if framework support return array
 */
interface ProductGroupPriceInterface
{
    /**
     * Retrieve customer group id
     *
     * @return int
     */
    public function getCustomerGroupId();

    /**
     * Retrieve price value
     *
     * @return float
     */
    public function getValue();
}

