<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

class GroupPriceBuilder extends AbstractObjectBuilder
{
    /**
     * Set customer group id
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->_set(GroupPrice::CUSTOMER_GROUP_ID, $customerGroupId);
        return $this;
    }

    /**
     * Set price value
     *
     * @param float $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->_set(GroupPrice::VALUE, $value);
        return $this;
    }
}
