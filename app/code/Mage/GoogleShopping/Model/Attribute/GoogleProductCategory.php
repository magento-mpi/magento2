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
 * GoogleProductCategory attribute model
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Attribute_GoogleProductCategory extends Mage_GoogleShopping_Model_Attribute_Default
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
        $targetCountry = Mage::getSingleton('Mage_GoogleShopping_Model_Config')
            ->getTargetCountry($product->getStoreId());
        $value = Mage::getModel('Mage_GoogleShopping_Model_Type')
            ->loadByAttributeSetId($product->getAttributeSetId(), $targetCountry);

        $val = ($value->getCategory() == Mage_GoogleShopping_Helper_Category::CATEGORY_OTHER)
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
