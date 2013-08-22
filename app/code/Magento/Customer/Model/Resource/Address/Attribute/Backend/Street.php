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
 * Customer password attribute backend
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Resource_Address_Attribute_Backend_Street
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare object for save
     *
     * @param Magento_Object $object
     * @return Magento_Customer_Model_Resource_Address_Attribute_Backend_Street
     */
    public function beforeSave($object)
    {
        $street = $object->getStreet(-1);
        if ($street) {
            $object->implodeStreetAddress();
        }
        return $this;
    }
}
