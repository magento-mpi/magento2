<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Condition attribute's model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_Condition extends Magento_GoogleShopping_Model_Attribute_Default
{
    /**
     * Available condition values
     *
     * @var string
     */
    const CONDITION_NEW = 'new';
    const CONDITION_USED = 'used';
    const CONDITION_REFURBISHED = 'refurbished';

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $availableConditions = array(
            self::CONDITION_NEW, self::CONDITION_USED, self::CONDITION_REFURBISHED
        );

        $mapValue = $this->getProductAttributeValue($product);
        if (!is_null($mapValue) && in_array($mapValue, $availableConditions)) {
            $condition = $mapValue;
        } else {
            $condition = self::CONDITION_NEW;
        }

        return $this->_setAttribute($entry, 'condition', self::ATTRIBUTE_TYPE_TEXT, $condition);
    }
}
