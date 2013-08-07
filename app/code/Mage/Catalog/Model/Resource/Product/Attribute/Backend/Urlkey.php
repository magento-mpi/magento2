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
 * Product url key attribute backend
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before save
     *
     * @param Magento_Object $object
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }

    /**
     * Refresh product rewrites
     *
     * @param Magento_Object $object
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
     */
    public function afterSave($object)
    {
        if ($object->dataHasChangedFor($this->getAttribute()->getName())) {
            Mage::getSingleton('Mage_Catalog_Model_Url')->refreshProductRewrites(null, $object, true);
        }
        return $this;
    }
}
