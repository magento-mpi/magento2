<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Attribute\Backend;

/**
 * Invoice backend model for child attribute
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Child extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Perform operation before save
     *
     * @param \Magento\Framework\Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if ($object->getOrder()) {
            $object->setParentId($object->getOrder()->getId());
        }
        parent::beforeSave($object);
        return $this;
    }
}
