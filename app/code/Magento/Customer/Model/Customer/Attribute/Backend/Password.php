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
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Customer\Attribute\Backend;

class Password extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    const MIN_PASSWORD_LENGTH = 6;

    /**
     * Core string
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_coreString = null;

    /**
     * @param \Magento\Core\Helper\String $coreString
     */
    public function __construct(
        \Magento\Core\Helper\String $coreString
    ) {
        $this->_coreString = $coreString;
    }

    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     *
     * @param \Magento\Object $object
     */
    public function beforeSave($object)
    {
        $password = $object->getPassword();
        /** @var \Magento\Core\Helper\String $stringHelper */
        $stringHelper = $this->_coreString;

        $length = $stringHelper->strlen($password);
        if ($length > 0) {
            if ($length < self::MIN_PASSWORD_LENGTH) {
                \Mage::throwException(__('The password must have at least %1 characters.', self::MIN_PASSWORD_LENGTH));
            }

            if ($stringHelper->substr($password, 0, 1) == ' ' ||
                $stringHelper->substr($password, $length - 1, 1) == ' ') {
                \Mage::throwException(__('The password can not begin or end with a space.'));
            }

            $object->setPasswordHash($object->hashPassword($password));
        }
    }

    /**
     * @param \Magento\Object $object
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
