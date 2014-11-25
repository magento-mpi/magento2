<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class GroupPrice extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Catalog\Api\Data\ProductGroupPriceInterface
{
    /**
     * Retrieve customer group id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->getData('customer_group_id');
    }

    /**
     * Retrieve price value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->getData('value');
    }
}
