<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer password attribute backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Password extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     */
    public function beforeSave($object)
    {
        $password = trim($object->getPassword());
        $len = Mage::helper('Mage_Core_Helper_String')->strlen($password);
        if ($len) {
             if ($len < 6) {
                Mage::throwException(Mage::helper('Mage_Customer_Helper_Data')->__('The password must have at least 6 characters. Leading or trailing spaces will be ignored.'));
            }
            $object->setPasswordHash($object->hashPassword($password));
        }
    }

    public function validate($object)
    {
        if ($password = $object->getPassword()) {
            if ($password == $object->getPasswordConfirm()) {
                return true;
            }
        }

        return parent::validate($object);
    }

}
