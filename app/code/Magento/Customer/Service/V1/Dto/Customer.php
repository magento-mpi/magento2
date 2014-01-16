<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class Customer
 * Uses array to hold data, setters return $this so they can be chained.
 *
 * @package Magento\Customer\Service\Entity\V1
 */
class Customer extends \Magento\Service\Entity\AbstractDto implements Eav\EntityInterface
{

    /**
     * @var array  Special attribute codes which cannot be set or gotten
     * they are used by the model but should not be exposed in the DTO
     */
    private static $_nonAttributes = [self::ID];

    /**
     * name of field containing entity id, used to exclude this field from list of attributes.
     */
    const ID = 'id';

    /**
     * constants defined for keys of array, makes typos less likely
     */
    const CONFIRMATION = 'confirmation';
    const CREATED_AT = 'created_at';
    const CREATED_IN = 'created_in';
    const DOB = 'dob';
    const EMAIL = 'email';
    const FIRSTNAME = 'firstname';
    const GENDER = 'gender';
    const GROUP_ID = 'group_id';
    const LASTNAME = 'lastname';
    const MIDDLENAME = 'middlename';
    const PREFIX = 'prefix';
    const STORE_ID = 'store_id';
    const SUFFIX = 'suffix';
    const TAXVAT = 'taxvat';
    const WEBSITE_ID = 'website_id';
    const DEFAULT_BILLING = 'default_billing';
    const DEFAULT_SHIPPING = 'default_shipping';

    /**
     * Retrieve array of all attributes, in the form of 'attribute code' => <attribute value'
     *
     * @return array|\ArrayAccess|\string[]
     */
    public function getAttributes()
    {
        $attributes = $this->__toArray();
        foreach (self::$_nonAttributes as $keyName) {
            unset ($attributes[$keyName]);
        }
        return $attributes;
    }

    /**
     * Gets an attribute value.
     *
     * @param string $attributeCode
     * @return mixed The attribute value or null if the attribute has not been set
     */
    public function getAttribute($attributeCode)
    {
        if (isset($this->_data[$attributeCode])) {
            return $this->_data[$attributeCode];
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getDefaultBilling()
    {
        return $this->_get(self::DEFAULT_BILLING);
    }

    /**
     * @return string
     */
    public function getDefaultShipping()
    {
        return $this->_get(self::DEFAULT_SHIPPING);
    }

    /**
     * @return string
     */
    public function getConfirmation()
    {
        return $this->_get(self::CONFIRMATION);
    }

    /**
     * @param string $confirmation
     * @return Customer
     */
    public function setConfirmation($confirmation)
    {
        return $this->_set(self::CONFIRMATION, $confirmation);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * @return string
     */
    public function getCreatedIn()
    {
        return $this->_get(self::CREATED_IN);
    }

    /**
     * @return string
     */
    public function getDob()
    {
        return $this->_get(self::DOB);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRSTNAME);
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->_get(self::GENDER);
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->_get(self::LASTNAME);
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->_get(self::MIDDLENAME);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->_get(self::PREFIX);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->_get(self::SUFFIX);
    }

    /**
     * @return string
     */
    public function getTaxvat()
    {
        return $this->_get(self::TAXVAT);
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID, 0);
    }
}
