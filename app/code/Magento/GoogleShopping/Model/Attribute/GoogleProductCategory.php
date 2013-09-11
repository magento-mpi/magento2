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
 * GoogleProductCategory attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Attribute;

class GoogleProductCategory extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $targetCountry = \Mage::getSingleton('Magento\GoogleShopping\Model\Config')
            ->getTargetCountry($product->getStoreId());
        $value = \Mage::getModel('\Magento\GoogleShopping\Model\Type')
            ->loadByAttributeSetId($product->getAttributeSetId(), $targetCountry);

        $val = ($value->getCategory() == \Magento\GoogleShopping\Helper\Category::CATEGORY_OTHER)
            ? ''
            : $value->getCategory();

        $this->_setAttribute(
            $entry,
            'google_product_category',
            self::ATTRIBUTE_TYPE_TEXT,
            htmlspecialchars_decode($val, ENT_NOQUOTES)
        );
        return $entry;
    }
}
