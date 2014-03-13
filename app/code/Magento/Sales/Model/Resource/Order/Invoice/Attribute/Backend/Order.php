<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Invoice\Attribute\Backend;

/**
 * Invoice backend model for order attribute
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Order
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Method is invoked before save
     *
     * @param \Magento\Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if ($object->getOrder()) {
            $object->setOrderId($object->getOrder()->getId());
            $object->setBillingAddressId($object->getOrder()->getBillingAddress()->getId());
        }
        return parent::beforeSave($object);
    }
}
