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
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Catalog_Model_Product_Attribute_Backend_Urlkey extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false) {
            return $this;
        }
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }

    public function afterSave($object)
    {
        /* @var $object Magento_Catalog_Model_Product */
        /**
         * Logic moved to Magento_Catalog_Model_Indexer_Url
         */
        /*if (!$object->getExcludeUrlRewrite() &&
            ($object->dataHasChangedFor('url_key') || $object->getIsChangedCategories() || $object->getIsChangedWebsites())) {
            Mage::getSingleton('Magento_Catalog_Model_Url')->refreshProductRewrite($object->getId());
        }*/
        return $this;
    }
}
