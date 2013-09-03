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
 * Order billing address backend
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Attribute_Backend_Billing extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Perform operation before save
     *
     * @param \Magento\Object $object
     */
    public function beforeSave($object)
    {
        $billingAddressId = $object->getBillingAddressId();
        if (is_null($billingAddressId)) {
            $object->unsetBillingAddressId();
        }
    }

    /**
     * Perform operation after save
     *
     * @param \Magento\Object $object
     */
    public function afterSave($object)
    {
        $billingAddressId = false;
        foreach ($object->getAddressesCollection() as $address) {
            if ('billing' == $address->getAddressType()) {
                $billingAddressId = $address->getId();
            }
        }
        if ($billingAddressId) {
            $object->setBillingAddressId($billingAddressId);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getAttributeCode());
        }
    }
}
