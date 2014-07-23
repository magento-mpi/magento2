<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Customer data builder for quote
 *
 * @codeCoverageIgnore
 */
class CustomerBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set customer id
     *
     * @param int|null $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(self::ID, $value);
    }

    /**
     * Set customer tax class id
     *
     * @param int|null $value
     * @return $this
     */
    public function setTaxClassId($value)
    {
        return $this->_set(self::TAX_CLASS_ID, $value);
    }

    /**
     * Set customer group id
     *
     * @param int|null $value
     * @return $this
     */
    public function setGroupId($value)
    {
        return $this->_set(self::GROUP_ID, $value);
    }

    /**
     * Set customer email
     *
     * @param string|null $value
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->_set(self::EMAIL, $value);
    }

    /**
     * Set customer name prefix
     *
     * @param string|null $value
     * @return $this
     */
    public function setPrefix($value)
    {
        return $this->_set(self::PREFIX, $value);
    }

    /**
     * Set customer first name
     *
     * @param string|null $value
     * @return $this
     */
    public function setFirstName($value)
    {
        return $this->_set(self::FIRST_NAME, $value);
    }

    /**
     * Set customer middle name
     *
     * @param string|null $value
     * @return $this
     */
    public function setMiddleName($value)
    {
        return $this->_set(self::MIDDLE_NAME, $value);
    }

    /**
     * Set customer last name
     *
     * @param string|null $value
     * @return $this
     */
    public function setLastName($value)
    {
        return $this->_set(self::LAST_NAME, $value);
    }

    /**
     * Set customer name suffix
     *
     * @param string|null $value
     * @return $this
     */
    public function setSuffix($value)
    {
        return $this->_set(self::SUFFIX, $value);
    }

    /**
     * Set customer date of birth
     *
     * @param mixed|null $value
     * @return $this
     */
    public function setDob($value)
    {
        return $this->_set(self::DOB, $value);
    }

    /**
     * Set note
     *
     * @param string|null $value
     * @return $this
     */
    public function setNote($value)
    {
        return $this->_set(self::NOTE, $value);
    }

    /**
     * Set notification status
     *
     * @param string|null $value
     * @return $this
     */
    public function setNoteNotify($value)
    {
        return $this->_set(self::NOTE_NOTIFY, $value);
    }

    /**
     * Is customer a guest?
     *
     * @param bool $value
     * @return $this
     */
    public function setIsGuest($value)
    {
        return (bool)$this->_set(self::IS_GUEST, $value);
    }

    /**
     * Set password hash
     *
     * @param string $value
     * @return $this
     */
    public function getPasswordHash($value)
    {
        return $this->_set(self::PASSWORD_HASH, $value);
    }

    /**
     * Get  taxvat value
     *
     * @param string $value
     * @return $this
     */
    public function getTaxVat($value)
    {
        return $this->_get(self::TAXVAT, $value);
    }

    /**
     * Get gender
     *
     * @param string $value
     * @return $this
     */
    public function getGender($value)
    {
        return $this->_get(self::GENDER, $value);
    }

}
