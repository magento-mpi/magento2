<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Product;

use Magento\Framework\Service\Data\Eav\AbstractObject;

class GroupPrice extends AbstractObject
{
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const VALUE = 'value';

    /**
     * Retrieve customer group id
     *
     * @return string
     */
    public function getCustomerGroupId()
    {
        return $this->_get(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Retrieve price value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
