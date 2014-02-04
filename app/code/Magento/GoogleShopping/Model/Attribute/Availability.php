<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Attribute;

/**
 * Availability attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Availability extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * @var array
     */
    protected $_googleAvailabilityMap = array(
        0 => 'out of stock',
        1 => 'in stock'
    );

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param \Magento\Catalog\Model\Product $product
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
