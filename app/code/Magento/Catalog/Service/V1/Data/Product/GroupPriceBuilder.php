<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

use Magento\Framework\Api\ExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class GroupPriceBuilder extends ExtensibleObjectBuilder
{
    /**
     * Set customer group id
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->_set(GroupPrice::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * Set price value
     *
     * @param float $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(GroupPrice::VALUE, $value);
    }
}
