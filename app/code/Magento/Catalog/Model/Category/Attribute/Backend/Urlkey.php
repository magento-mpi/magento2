<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category\Attribute\Backend;

/**
 * Category url key attribute backend
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Urlkey extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * TODO: Enter description here...
     *
     * @param \Magento\Framework\Object $object
     * @return $this
     */
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
}
