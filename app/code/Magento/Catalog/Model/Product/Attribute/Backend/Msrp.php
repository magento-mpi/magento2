<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute for `Apply MAP` enable/disable option
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Msrp extends Magento_Catalog_Model_Product_Attribute_Backend_Boolean
{
    /**
     * Disable MAP if it's bundle with dynamic price type
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function beforeSave($product)
    {
        if (!($product instanceof Magento_Catalog_Model_Product)
            || $product->getTypeId() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE
            || $product->getPriceType() != Magento_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
        ) {
            return parent::beforeSave($product);
        }

        parent::beforeSave($product);
        $attributeCode = $this->getAttribute()->getName();
        $value = $product->getData($attributeCode);
        if (empty($value)) {
            $value = Mage::helper('Magento_Catalog_Helper_Data')->isMsrpApplyToAll();
        }
        if ($value) {
            $product->setData($attributeCode, 0);
        }
        return $this;
    }
}
