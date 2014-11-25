<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api;

interface TaxClassManagementInterface
{
    /**#@+
     * Tax class type.
     */
    const TYPE_CUSTOMER = 'CUSTOMER';
    const TYPE_PRODUCT = 'PRODUCT';
    /**#@-*/

    /**
     * Get tax class id
     *
     * @param \Magento\Tax\Api\Data\TaxClassKeyInterface|null $taxClassKey
     * @param string $taxClassType
     * @return int|null
     */
    public function getTaxClassId($taxClassKey, $taxClassType = self::TYPE_PRODUCT);
}
