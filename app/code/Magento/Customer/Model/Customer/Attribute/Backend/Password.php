<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Customer\Attribute\Backend;

/**
 * Customer password attribute backend
 */
class Password extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Min password length
     */
    const MIN_PASSWORD_LENGTH = 6;

    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\StringIconv
     */
    protected $stringIconv;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Stdlib\StringIconv $stringIconv
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Stdlib\StringIconv $stringIconv
    ) {
        $this->stringIconv = $stringIconv;
        parent::__construct($logger);
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

        $length = $this->stringIconv->strlen($password);
        if ($length > 0) {
            if ($length < self::MIN_PASSWORD_LENGTH) {
                throw new \Magento\Core\Exception(
                    __('The password must have at least %1 characters.', self::MIN_PASSWORD_LENGTH)
                );
            }

            if ($this->stringIconv->substr($password, 0, 1) == ' ' ||
                $this->stringIconv->substr($password, $length - 1, 1) == ' ') {
                throw new \Magento\Core\Exception(__('The password can not begin or end with a space.'));
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
        $password = $object->getPassword();
        if ($password && $password == $object->getPasswordConfirm()) {
            return true;
        }

        return parent::validate($object);
    }
}
