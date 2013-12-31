<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

use Magento\Service\Entity\AbstractDto;

/**
 * Class Customer
 * Uses array to hold data, setters return $this so they can be chained.
 *
 * @package Magento\Customer\Service\Entity\V1
 */
class Customer extends AbstractDto implements Eav\EntityInterface
{

    /**
     * @var array  Special attribute codes which cannot be set or gotten
     * they are used by the model but should not be exposed in the DTO
     */
    private $_nonAttributes = [self::ID];

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
     * @return array|\ArrayAccess|\string[]
     */
    public function getAttributes()
    {
        $attributes = $this->__toArray();
        foreach ($this->_nonAttributes as $keyName) {
            unset ($attributes[$keyName]);
        }
        return $attributes;
    }

    /**
     * Sets the customer's attributes.
     *
     * Must be in the form of 'attribute code' => 'attribute value'
     * @param array $attributes
     * @return Customer
     * @throws Exception
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $attributeCode => $value) {
            $this->setAttribute($attributeCode, $value);
        }
        return $this;
    }

    /**
     * Set a customer attribute value.
     *
     * @param $attributeCode
     * @param $value
     * @return $this
     * @throws Exception
     */
    public function setAttribute($attributeCode, $value)
    {
        if (in_array($attributeCode, $this->_nonAttributes)) {
            throw new Exception('Cannot set or change attribute ' . $attributeCode);
        }
        $this->_data[$attributeCode] = $value;
        return $this;
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
     * @param string $createdAt
     * @return Customer
     */
    public function setCreatedAt($createdAt)
    {
        return $this->_set(self::CREATED_AT, $createdAt);
    }


    /**
     * @return string
     */
    public function getCreatedIn()
    {
        return $this->_get(self::CREATED_IN);
    }

    /**
     * @param string $createdIn
     * @return Customer
     */
    public function setCreatedIn($createdIn)
    {
        return $this->_set(self::CREATED_IN, $createdIn);
    }

    /**
     * @return string
     */
    public function getDob()
    {
        return $this->_get(self::DOB);
    }

    /**
     * @param string $dob
     * @return Customer
     */
    public function setDob($dob)
    {
        return $this->_set(self::DOB, $dob);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        return $this->_set(self::EMAIL, $email);
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRSTNAME);
    }

    /**
     * @param string $firstname
     * @return Customer
     */
    public function setFirstname($firstname)
    {
        return $this->_set(self::FIRSTNAME, $firstname);
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->_get(self::GENDER);
    }

    /**
     * @param string $gender
     * @return Customer
     */
    public function setGender($gender)
    {
        return $this->_set(self::GENDER, $gender);
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * @param string $groupId
     * @return Customer
     */
    public function setGroupId($groupId)
    {
        return $this->_set(self::GROUP_ID, $groupId);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @param int $id
     * @return Customer
     */
    public function setCustomerId($id)
    {
        return $this->_set(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->_get(self::LASTNAME);
    }

    /**
     * @param string $lastname
     * @return Customer
     */
    public function setLastname($lastname)
    {
        return $this->_set(self::LASTNAME, $lastname);
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->_get(self::MIDDLENAME);
    }

    /**
     * @param string $middlename
     * @return Customer
     */
    public function setMiddlename($middlename)
    {
        return $this->_set(self::MIDDLENAME, $middlename);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->_get(self::PREFIX);
    }

    /**
     * @param string $prefix
     * @return Customer
     */
    public function setPrefix($prefix)
    {
        return $this->_set(self::PREFIX, $prefix);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @param int $storeId
     * @return Customer
     */
    public function setStoreId($storeId)
    {
        return $this->_set(self::STORE_ID, $storeId);
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->_get(self::SUFFIX);
    }

    /**
     * @param string $suffix
     * @return Customer
     */
    public function setSuffix($suffix)
    {
        return $this->_set(self::SUFFIX, $suffix);
    }

    /**
     * @return string
     */
    public function getTaxvat()
    {
        return $this->_get(self::TAXVAT);
    }

    /**
     * @param string $taxvat
     * @return Customer
     */
    public function setTaxvat($taxvat)
    {
        return $this->_set(self::TAXVAT, $taxvat);
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID, 0);
    }

    /**
     * @param int $websiteId
     * @return Customer
     */
    public function setWebsiteId($websiteId)
    {
        return $this->_set(self::WEBSITE_ID, $websiteId);
    }
}
