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
 * Google Content Product Helper
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Helper_Product extends Magento_Core_Helper_Abstract
{
    /**
     * Product attributes cache
     *
     * @var array
     */
    protected $_productAttributes;

    /**
     * Attribute labels by store ID
     *
     * @var array
     */
    protected $_attributeLabels;

    /**
     * Return Product attribute by attribute's ID
     *
     * @param Magento_Catalog_Model_Product $product
     * @param int $attributeId
     * @return null|Magento_Catalog_Model_Entity_Attribute Product's attribute
     */
    public function getProductAttribute(Magento_Catalog_Model_Product $product, $attributeId)
    {
        if (!isset($this->_productAttributes[$product->getId()])) {
            $attributes = $product->getAttributes();
            foreach ($attributes as $attribute) {
                $this->_productAttributes[$product->getId()][$attribute->getAttributeId()] = $attribute;
            }
        }

        return isset($this->_productAttributes[$product->getId()][$attributeId])
            ? $this->_productAttributes[$product->getId()][$attributeId]
            : null;
    }

    /**
     * Return Product Attribute Store Label
     * Set attribute name like frontend lable for custom attributes (which wasn't defined by Google)
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param int $storeId Store View Id
     * @return string Attribute Store View Label or Attribute code
     */
    public function getAttributeLabel($attribute, $storeId)
    {
        $attributeId = $attribute->getId();
        $frontendLabel = $attribute->getFrontend()->getLabel();

        if (is_array($frontendLabel)) {
            $frontendLabel = array_shift($frontendLabel);
        }
        if (!isset($this->_attributeLabels[$attributeId])) {
            $this->_attributeLabels[$attributeId] = $attribute->getStoreLabels();
        }

        if (isset($this->_attributeLabels[$attributeId][$storeId])) {
            return $this->_attributeLabels[$attributeId][$storeId];
        } else if (!empty($frontendLabel)) {
            return $frontendLabel;
        } else {
            return $attribute->getAttributeCode();
        }
    }
}
