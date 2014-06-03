<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Creditmemo\Attribute\Backend;

/**
 * Invoice backend model for child attribute
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Child extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Method is invoked before save
     *
     * @param \Magento\Framework\Object $object
     * @return \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
     */
    public function beforeSave($object)
    {
        if ($object->getCreditmemo()) {
            $object->setParentId($object->getCreditmemo()->getId());
        }
        return parent::beforeSave($object);
    }
}
