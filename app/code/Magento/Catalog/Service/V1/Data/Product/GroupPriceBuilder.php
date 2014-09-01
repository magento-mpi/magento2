<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class GroupPriceBuilder extends AbstractExtensibleObjectBuilder
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
