<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data\OrderTaxDetails;

class Item extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_TYPE = 'type';

    const KEY_ITEM_ID = 'item_id';

    const KEY_ASSOCIATED_ITEM_ID = 'associated_item_id';

    const KEY_APPLIED_TAXES = 'applied_taxes';
    /**#@-*/

    /**
     * Get type (shipping, product, weee, gift wrapping, etc)
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->_get(self::KEY_TYPE);
    }

    /**
     * Return item id if this item is a product
     *
     * @return int|null
     */
    public function getItemId()
    {
        return $this->_get(self::KEY_ITEM_ID);
    }

    /**
     * Return associated item id if this item is associated with another item, null otherwise
     *
     * @return int|null
     */
    public function getAssociatedItemId()
    {
        return $this->_get(self::KEY_ASSOCIATED_ITEM_ID);
    }

    /**
     * Get applied taxes
     *
     * @return \Magento\Tax\Service\V1\Data\OrderTaxDetails\AppliedTax[]|null
     */
    public function getAppliedTaxes()
    {
        return $this->_get(self::KEY_APPLIED_TAXES);
    }
}
