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
 * @todo remove this interface if framework support return array
 */
interface ProductGroupPriceInterface
{
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const VALUE = 'value';

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
