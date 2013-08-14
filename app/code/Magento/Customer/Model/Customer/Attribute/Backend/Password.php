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
 * @category   Mage
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Attribute_Backend_Password extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    const MIN_PASSWORD_LENGTH = 6;

    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     *
     * @param Magento_Object $object
     */
    public function beforeSave($object)
    {
        $password = $object->getPassword();
        /** @var Magento_Core_Helper_String $stringHelper */
        $stringHelper = Mage::helper('Magento_Core_Helper_String');

        $length = $stringHelper->strlen($password);
        if ($length > 0) {
            if ($length < self::MIN_PASSWORD_LENGTH) {
                Mage::throwException(Mage::helper('Magento_Customer_Helper_Data')->
                    __('The password must have at least %s characters.', self::MIN_PASSWORD_LENGTH));
            }

            if ($stringHelper->substr($password, 0, 1) == ' ' ||
                $stringHelper->substr($password, $length - 1, 1) == ' ') {
                Mage::throwException(Mage::helper('Magento_Customer_Helper_Data')->
                    __('The password can not begin or end with a space.'));
            }

            $object->setPasswordHash($object->hashPassword($password));
        }
    }

    /**
     * @param Magento_Object $object
     * @return bool
     */
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
