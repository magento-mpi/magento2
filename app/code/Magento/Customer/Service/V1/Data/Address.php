<?php
/**
 * Address class acts as a Data Object for the Customer Service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

class Address extends \Magento\Service\Entity\AbstractDto
{
    const ADDRESS_TYPE_BILLING = 'billing';
    const ADDRESS_TYPE_SHIPPING = 'shipping';
    const KEY_COUNTRY_ID = 'country_id';
    const KEY_DEFAULT_BILLING = 'default_billing';
    const KEY_DEFAULT_SHIPPING = 'default_shipping';
    const KEY_ID = 'id';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_REGION = Region::KEY_REGION;
    const KEY_REGION_ID = Region::KEY_REGION_ID;
    const KEY_STREET = 'street';
    const KEY_COMPANY = 'company';
    const KEY_TELEPHONE = 'telephone';
    const KEY_FAX = 'fax';
    const KEY_POSTCODE = 'postcode';
    const KEY_CITY = 'city';
    const KEY_FIRSTNAME = 'firstname';
    const KEY_LASTNAME = 'lastname';
    const KEY_MIDDLENAME = 'middlename';
    const KEY_PREFIX = 'prefix';
    const KEY_SUFFIX = 'suffix';
    const KEY_VAT_ID = 'vat_id';

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::KEY_ID);
    }

    /**
     * @return boolean|null
     */
    public function isDefaultShipping()
    {
        return $this->_get(self::KEY_DEFAULT_SHIPPING);
    }

    /**
     * @return boolean|null
     */
    public function isDefaultBilling()
    {
        return $this->_get(self::KEY_DEFAULT_BILLING);
    }

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return string|null The attribute value or null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode)
    {
        if (isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            && array_key_exists($attributeCode, $this->_data[self::CUSTOM_ATTRIBUTES_KEY])
        ) {
            return $this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode];
        } else {
            return null;
        }
    }

    /**
     * Retrieve custom attributes values as an associative array.
     *
     * @return string[]
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY]
            : [];
    }

    /**
     * @return Region|null
     */
    public function getRegion()
    {
        return $this->_get(self::KEY_REGION);
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->_get(self::KEY_COUNTRY_ID);
    }

    /**
     * @return \string[]|null
     */
    public function getStreet()
    {
        return $this->_get(self::KEY_STREET);
    }

    /**
     * @return string|null
     */
    public function getCompany()
    {
        return $this->_get(self::KEY_COMPANY);
    }

    /**
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_get(self::KEY_TELEPHONE);
    }

    /**
     * @return string|null
     */
    public function getFax()
    {
        return $this->_get(self::KEY_FAX);
    }

    /**
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->_get(self::KEY_POSTCODE);
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::KEY_CITY);
    }

    /**
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->_get(self::KEY_FIRSTNAME);
    }

    /**
     * @return string|null
     */
    public function getLastname()
    {
        return $this->_get(self::KEY_LASTNAME);
    }

    /**
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->_get(self::KEY_MIDDLENAME);
    }

    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->_get(self::KEY_PREFIX);
    }

    /**
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->_get(self::KEY_SUFFIX);
    }

    /**
     * @return string|null
     */
    public function getVatId()
    {
        return $this->_get(self::KEY_VAT_ID);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::KEY_CUSTOMER_ID);
    }
}
