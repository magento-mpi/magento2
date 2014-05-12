<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Id attribute model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Attribute;

class Id extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Gdata\Gshopping\Entry $entry
     * @return \Magento\Framework\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $value = $this->_gsData->buildContentProductId($product->getId(), $product->getStoreId());
        return $this->_setAttribute($entry, 'id', self::ATTRIBUTE_TYPE_TEXT, $value);
    }
}
