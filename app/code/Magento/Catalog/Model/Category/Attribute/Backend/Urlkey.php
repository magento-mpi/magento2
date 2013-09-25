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
 * Category url key attribute backend
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Category_Attribute_Backend_Urlkey extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Enter description here...
     *
     * @param Magento_Object $object
     * @return Magento_Catalog_Model_Category_Attribute_Backend_Urlkey
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false) {
            return $this;
        }
        if ($urlKey=='') {
            $urlKey = $object->getName();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }
}
