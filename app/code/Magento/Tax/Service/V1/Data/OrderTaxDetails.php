<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

class OrderTaxDetails extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_APPLIED_TAXES = 'applied_taxes';

    const KEY_ITEMS = 'items';

    /**#@-*/

    /**
     * Get applied taxes at order level
     *
     * @return \Magento\Tax\Service\V1\Data\OrderTaxDetails\AppliedTax[] | null
     */
    public function getAppliedTaxes()
    {
        return $this->_get(self::KEY_APPLIED_TAXES);
    }

    /**
     * Get order item tax details
     *
     * @return \Magento\Tax\Service\V1\Data\OrderTaxDetails\Item[] | null
     */
    public function getItems()
    {
        return $this->_get(self::KEY_ITEMS);
    }
}
