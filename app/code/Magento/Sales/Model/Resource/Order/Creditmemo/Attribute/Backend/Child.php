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
namespace Magento\Sales\Model\Resource\Order\Creditmemo\Attribute\Backend;

class Child
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Method is invoked before save
     *
     * @param \Magento\Object $object
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
