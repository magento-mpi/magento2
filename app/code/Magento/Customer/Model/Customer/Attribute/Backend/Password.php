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

use Magento\Framework\Model\Exception;

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
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Stdlib\String $string
     */
    public function __construct(\Magento\Framework\Logger $logger, \Magento\Framework\Stdlib\String $string)
    {
        $this->string = $string;
        parent::__construct($logger);
    }

    /**
     * Special processing before attribute save:
     * a) check some rules for password
     * b) transform temporary attribute 'password' into real attribute 'password_hash'
     *
     * @param \Magento\Object $object
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function beforeSave($object)
    {
        $password = $object->getPassword();

        $length = $this->string->strlen($password);
        if ($length > 0) {
            if ($length < self::MIN_PASSWORD_LENGTH) {
                throw new Exception(__('The password must have at least %1 characters.', self::MIN_PASSWORD_LENGTH));
            }

            if ($this->string->substr(
                $password,
                0,
                1
            ) == ' ' || $this->string->substr(
                $password,
                $length - 1,
                1
            ) == ' '
            ) {
                throw new Exception(__('The password can not begin or end with a space.'));
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
