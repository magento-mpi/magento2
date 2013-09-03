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
 * Product url key attribute backend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before save
     *
     * @param \Magento\Object $object
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
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
     * @param \Magento\Object $object
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey
     */
    public function afterSave($object)
    {
        if ($object->dataHasChangedFor($this->getAttribute()->getName())) {
            Mage::getSingleton('Magento_Catalog_Model_Url')->refreshProductRewrites(null, $object, true);
        }
        return $this;
    }
}
