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

namespace Magento\Catalog\Model\Product\Attribute\Backend;

class Urlkey extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
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
        /* @var $object \Magento\Catalog\Model\Product */
        /**
         * Logic moved to \Magento\Catalog\Model\Indexer\Url
         */
        /*if (!$object->getExcludeUrlRewrite() &&
            ($object->dataHasChangedFor('url_key') || $object->getIsChangedCategories() || $object->getIsChangedWebsites())) {
            \Mage::getSingleton('Magento\Catalog\Model\Url')->refreshProductRewrite($object->getId());
        }*/
        return $this;
    }
}
