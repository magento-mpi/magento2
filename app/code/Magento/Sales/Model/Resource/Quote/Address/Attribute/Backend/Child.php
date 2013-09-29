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
 * Quote address attribute backend child resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend;

class Child
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Set store id to the attribute
     *
     * @param \Magento\Object $object
     * @return \Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend\Child
     */
    public function beforeSave($object)
    {
        if ($object->getAddress()) {
            $object->setParentId($object->getAddress()->getId())
                ->setStoreId($object->getAddress()->getStoreId());
        }
        parent::beforeSave($object);
        return $this;
    }
}
