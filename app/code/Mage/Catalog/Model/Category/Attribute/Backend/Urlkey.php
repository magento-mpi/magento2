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
 * Category url key attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Enter description here...
     *
     * @param Magento_Object $object
     * @return Mage_Catalog_Model_Category_Attribute_Backend_Urlkey
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

    /**
     * Enter description here...
     *
     * @param Magento_Object $object
     */
    public function afterSave($object)
    {
        /* @var $object Mage_Catalog_Model_Category */
        /**
         * Logic moved to Mage_Catalog_Molde_Indexer_Url
         */
        /*if (!$object->getInitialSetupFlag() && $object->getLevel() > 1) {
            if ($object->dataHasChangedFor('url_key') || $object->getIsChangedProductList()) {
                Mage::getSingleton('Mage_Catalog_Model_Url')->refreshCategoryRewrite($object->getId());
            }
        }*/
    }

}
