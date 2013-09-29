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
namespace Magento\Catalog\Model\Resource\Product\Attribute\Backend;

class Urlkey
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Before save
     *
     * @param \Magento\Object $object
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Urlkey
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
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Urlkey
     */
    public function afterSave($object)
    {
        if ($object->dataHasChangedFor($this->getAttribute()->getName())) {
            \Mage::getSingleton('Magento\Catalog\Model\Url')->refreshProductRewrites(null, $object, true);
        }
        return $this;
    }
}
