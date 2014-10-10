<?php
/**
 * Group Price
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data\Product;

/**
 * Interface GroupPrice must be implemented Magento\Catalog\Model\Product\Type\Price\Group  model
 * @see \Magento\Catalog\Service\V1\Data\Product\GroupPrice
 */
interface GroupPriceInterface
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

