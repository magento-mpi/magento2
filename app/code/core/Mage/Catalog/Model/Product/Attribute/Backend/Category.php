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
 * Catalog product categories backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Category extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterLoad($object)
    {
        $object->setData($this->getAttribute()->getAttributeCode(), $object->getCategoryIds());
        return parent::afterLoad($object);
    }

    public function beforeSave($object)
    {
        $object->setData(
            $this->getAttribute()->getAttributeCode(),
            $object->getData($this->getAttribute()->getAttributeCode())
        );
        return parent::beforeSave($object);
    }
}
