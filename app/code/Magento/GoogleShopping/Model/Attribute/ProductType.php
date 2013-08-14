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
 * ProductType attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_ProductType extends Magento_GoogleShopping_Model_Attribute_Default
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {

        $productCategories = $product->getCategoryIds();

        // TODO: set Default value for product_type attribute if product isn't assigned for any category
        $value = 'Shop';

        if (!empty($productCategories)) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load(
                array_shift($productCategories)
            );

            $breadcrumbs = array();

            foreach ($category->getParentCategories() as $cat) {
                $breadcrumbs[] = $cat->getName();
            }

            $value = implode(' > ', $breadcrumbs);
        }

        $this->_setAttribute($entry, 'product_type', self::ATTRIBUTE_TYPE_TEXT, $value);
        return $entry;
    }
}
