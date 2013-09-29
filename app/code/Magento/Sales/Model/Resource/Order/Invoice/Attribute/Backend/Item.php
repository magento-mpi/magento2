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
 * Invoice backend model for item attribute
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Invoice\Attribute\Backend;

class Item
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Method is invoked after save
     *
     * @param \Magento\Object $object
     * @return \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
     */
    public function afterSave($object)
    {
        if ($object->getOrderItem()) {
            $object->getOrderItem()->save();
        }
        return parent::beforeSave($object);
    }
}
