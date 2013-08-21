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
 * Customer default shipping address backend
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Attribute_Backend_Shipping extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $defaultShipping = $object->getDefaultShipping();
        if (is_null($defaultShipping)) {
            $object->unsetDefaultShipping();
        }
    }
    
    public function afterSave($object)
    {
        if ($defaultShipping = $object->getDefaultShipping()) 
        {
            $addressId = false;
            /**
             * post_index set in customer save action for address
             * this is $_POST array index for address
             */
            foreach ($object->getAddresses() as $address) {
                if ($address->getPostIndex() == $defaultShipping) {
                    $addressId = $address->getId();
                }
            }
            
            if ($addressId) {
                $object->setDefaultShipping($addressId);
                $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getAttributeCode());
            }
        }
    }
}
