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
 * Availability attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_Availability extends Magento_GoogleShopping_Model_Attribute_Default
{
    protected $_googleAvailabilityMap = array(
        0 => 'out of stock',
        1 => 'in stock'
    );

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $value = $this->_googleAvailabilityMap[(int)$product->isSalable()];
        $this->_setAttribute($entry, 'availability', self::ATTRIBUTE_TYPE_TEXT, $value);
        return $entry;
    }
}
