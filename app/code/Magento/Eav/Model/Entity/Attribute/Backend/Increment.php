<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity/Attribute/Model - attribute backend default
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity\Attribute\Backend;

class Increment extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Set new increment id
     *
     * @param \Magento\Object $object
     * @return \Magento\Eav\Model\Entity\Attribute\Backend\Increment
     */
    public function beforeSave($object)
    {
        if (!$object->getId()) {
            $this->getAttribute()->getEntity()->setNewIncrementId($object);
        }

        return $this;
    }
}
