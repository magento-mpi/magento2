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
 * Catalog product categories backend attribute model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Category extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set category ids to product data
     *
     * @param Magento_Catalog_Model_Product $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        $object->setData($this->getAttribute()->getAttributeCode(), $object->getCategoryIds());
        return parent::afterLoad($object);
    }
}
