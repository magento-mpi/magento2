<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sipping weight attribute model
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Attribute_ShippingWeight extends Mage_GoogleShopping_Model_Attribute_Default
{
    /**
     * Default weight unit
     *
     * @var string
     */
    const WEIGHT_UNIT = 'lb';

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $mapValue = $this->getProductAttributeValue($product);
        if (!$mapValue) {
            $weight = $this->getGroupAttributeWeight();
            $mapValue = $weight ? $weight->getProductAttributeValue($product) : null;
        }

        if ($mapValue) {
            $this->_setAttribute($entry, 'shipping_weight', self::ATTRIBUTE_TYPE_FLOAT, $mapValue, self::WEIGHT_UNIT);
        }

        return $entry;
    }
}
