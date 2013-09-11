<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Invoice backend model for child attribute
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Attribute\Backend;

class Child extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Perform operation before save
     *
     * @param \Magento\Object $object
     * @return \Magento\Sales\Model\Resource\Order\Attribute\Backend\Child
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
