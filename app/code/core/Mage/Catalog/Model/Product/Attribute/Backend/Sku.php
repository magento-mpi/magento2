<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product SKU backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Sku extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Maximum SKU string length
     *
     * @var string
     */
    const SKU_MAX_LENGTH = 64;

    /**
     * Validate SKU
     *
     * @param Mage_Catalog_Model_Product $object
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $helper = Mage::helper('Mage_Core_Helper_String');
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if ($this->getAttribute()->getIsRequired() && $this->getAttribute()->isValueEmpty($value)) {
            return false;
        }

        if ($helper->strlen($object->getSku()) > self::SKU_MAX_LENGTH) {
            Mage::throwException(
                Mage::helper('Mage_Catalog_Helper_Data')->__('SKU length should be %s characters maximum.', self::SKU_MAX_LENGTH)
            );
        }
        return true;
    }

    /**
     * Generate and set unique SKU to product
     *
     * @param $object Mage_Catalog_Model_Product
     */
    public function generateUniqueSku($object)
    {
        $attribute = $this->getAttribute();
        $entity = $attribute->getEntity();
        $increment = $entity->getLastSimilarAttributeValueIncrement($attribute, $object);
        $attributeValue = $object->getData($attribute->getAttributeCode());
        while (!$entity->checkAttributeUniqueValue($attribute, $object)) {
            $object->setSku(trim($attributeValue . '-' . ++$increment));
        }
    }
}
