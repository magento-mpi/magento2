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
 */
class Customer extends \Magento\Service\Entity\AbstractDto implements Eav\EntityInterface
{
    /**#@+
     * constants defined for keys of array, makes typos less likely
     */
    const ID = 'id';
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
    const RP_TOKEN = 'rp_token';
    const RP_TOKEN_CREATED_AT = 'rp_token_created_at';
    /**#@-*/

    /**
     * A list of valid customer DTO attributes.
     *
     * @var string[]
     */
    protected $_validAttributes = [
        self::ID,
        self::CONFIRMATION,
        self::CREATED_AT,
        self::CREATED_IN,
        self::DOB,
        self::EMAIL,
        self::FIRSTNAME,
        self::GENDER,
        self::GROUP_ID,
        self::LASTNAME,
        self::MIDDLENAME,
        self::PREFIX,
        self::STORE_ID,
        self::SUFFIX,
        self::TAXVAT,
        self::WEBSITE_ID,
        self::DEFAULT_BILLING,
        self::DEFAULT_SHIPPING,
        self::RP_TOKEN,
        self::RP_TOKEN_CREATED_AT,
    ];

    /**
     * Retrieve array of all attributes, in the form of 'attribute code' => <attribute value'
     *
     * @return string[] attributes, in the form of 'attribute code' => <attribute value'
     */
    public function getAttributes()
    {
        $unvalidatedData = $this->__toArray();
        $validData = [];
        foreach ($this->_validAttributes as $attributeCode) {
            if (array_key_exists($attributeCode, $unvalidatedData)) {
                $validData[$attributeCode] = $unvalidatedData[$attributeCode];
            }
        }
        return $validData;
    }

    /**
     * Get attribute value for given attribute code
     *
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode)
    {
        if (in_array($attributeCode, $this->_validAttributes) && isset($this->_data[$attributeCode])) {
            return $this->_data[$attributeCode];
        } else {
            return null;
        }
    }

    /**
     * Get default billing address id
     *
     * @return string
     */
    public function getDefaultBilling()
    {
        return $this->_get(self::DEFAULT_BILLING);
    }

    /**
     * Get default shipping address id
     *
     * @return string
     */
    public function getDefaultShipping()
    {
        return $this->_get(self::DEFAULT_SHIPPING);
    }

    /**
     * Get confirmation
     *
     * @return string
     */
    public function getConfirmation()
    {
        return $this->_get(self::CONFIRMATION);
    }

    /**
     * Get created at time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Get created in area
     *
     * @return string
     */
    public function getCreatedIn()
    {
        return $this->_get(self::CREATED_IN);
    }

    /**
     * Get date of birth
     *
     * @return string
     */
    public function getDob()
    {
        return $this->_get(self::DOB);
    }

    /**
     * Get email address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRSTNAME);
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->_get(self::GENDER);
    }

    /**
     * Get group id
     *
     * @return string
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->_get(self::LASTNAME);
    }

    /**
     * Get middle name
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->_get(self::MIDDLENAME);
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_get(self::PREFIX);
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Get suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->_get(self::SUFFIX);
    }

    /**
     * Get tax Vat
     * @return string
     */
    public function getTaxvat()
    {
        return $this->_get(self::TAXVAT);
    }

    /**
     * Get website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return (int)$this->_get(self::WEBSITE_ID);
    }

    /**
     * Get Rp token
     *
     * @return string
     */
    public function getRpToken()
    {
        return $this->_get(self::RP_TOKEN);
    }

    /**
     * Get Rp token created time
     *
     * @return string
     */
    public function getRpTokenCreatedAt()
    {
        return $this->_get(self::RP_TOKEN_CREATED_AT);
    }
}
