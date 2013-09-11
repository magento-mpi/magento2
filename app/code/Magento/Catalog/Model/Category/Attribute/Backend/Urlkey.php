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
namespace Magento\Catalog\Model\Category\Attribute\Backend;

class Urlkey extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * Enter description here...
     *
     * @param \Magento\Object $object
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Urlkey
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
     * @param \Magento\Object $object
     */
    public function afterSave($object)
    {
        /* @var $object \Magento\Catalog\Model\Category */
        /**
         * Logic moved to Magento_Catalog_Molde_Indexer_Url
         */
        /*if (!$object->getInitialSetupFlag() && $object->getLevel() > 1) {
            if ($object->dataHasChangedFor('url_key') || $object->getIsChangedProductList()) {
                \Mage::getSingleton('Magento\Catalog\Model\Url')->refreshCategoryRewrite($object->getId());
            }
        }*/
    }

}
