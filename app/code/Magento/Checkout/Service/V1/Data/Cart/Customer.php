<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Customer data for quote
 *
 * @codeCoverageIgnore
 */
class Customer extends \Magento\Framework\Service\Data\AbstractObject
{
    CONST ID = 'customer_id';

    CONST TAX_CLASS_ID = 'customer_tax_class_id';

    CONST GROUP_ID = 'customer_group_id';

    CONST EMAIL = 'customer_email';

    CONST PREFIX = 'customer_prefix';

    CONST FIRST_NAME = 'customer_firstname';

    CONST MIDDLE_NAME = 'customer_middlename';

    CONST LAST_NAME = 'customer_lastname';

    CONST SUFFIX = 'customer_suffix';

    CONST DOB = 'customer_dob';

    CONST NOTE = 'customer_note';

    CONST NOTE_NOTIFY = 'customer_note_notify';

    CONST IS_GUEST = 'customer_is_guest';

    CONST PASSWORD_HASH = 'password_hash';

    CONST TAXVAT = 'customer_taxvat';

    CONST GENDER = 'customer_gender';


    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get customer tax class id
     *
     * @return int|null
     */
    public function getTaxClassId()
    {
        return $this->_get(self::TAX_CLASS_ID);
    }

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Get customer name prefix
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->_get(self::PREFIX);
    }

    /**
     * Get customer first name
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->_get(self::FIRST_NAME);
    }

    /**
     * Get customer middle name
     *
     * @return string|null
     */
    public function getMiddleName()
    {
        return $this->_get(self::MIDDLE_NAME);
    }

    /**
     * Get customer last name
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->_get(self::LAST_NAME);
    }

    /**
     * Get customer name suffix
     *
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->_get(self::SUFFIX);
    }

    /**
     * Get customer date of birth
     *
     * @return mixed|null
     */
    public function getDob()
    {
        return $this->_get(self::DOB);
    }

    /**
     * Get note
     *
     * @return string|null
     */
    public function getNote()
    {
        return $this->_get(self::NOTE);
    }

    /**
     * Get notification status
     *
     * @return string|null
     */
    public function getNoteNotify()
    {
        return $this->_get(self::NOTE_NOTIFY);
    }

    /**
     * Is customer a guest?
     *
     * @return bool
     */
    public function getIsGuest()
    {
        return (bool)$this->_get(self::IS_GUEST);
    }

    /**
     * Get password hash
     *
     * @return string|null
     */
    public function getPasswordHash()
    {
        return $this->_get(self::PASSWORD_HASH);
    }

    /**
     * Get  taxvat value
     *
     * @return string|null
     */
    public function getTaxVat()
    {
        return $this->_get(self::TAXVAT);
    }

    /**
     * Get gender
     *
     * @return string|null
     */
    public function getGender()
    {
        return $this->_get(self::GENDER);
    }

}
