<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store attribute backend
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Customer\Attribute\Backend;

class Store extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Before save
     *
     * @param \Magento\Object $object
     * @return \Magento\Customer\Model\Customer\Attribute\Backend\Store
     */
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }

        if (!$object->hasStoreId()) {
            $object->setStoreId(\Mage::app()->getStore()->getId());
        }

        if (!$object->hasData('created_in')) {
            $object->setData('created_in', \Mage::app()->getStore($object->getStoreId())->getName());
        }

        return $this;
    }
}
