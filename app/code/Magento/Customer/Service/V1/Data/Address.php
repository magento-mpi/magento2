<?php
/**
 * Service Data Object defining the Address interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

class Address extends \Magento\Service\Data\EAV\AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const ADDRESS_TYPE_BILLING = 'billing';
    const ADDRESS_TYPE_SHIPPING = 'shipping';
    const KEY_COUNTRY_ID = 'country_id';
    const KEY_DEFAULT_BILLING = 'default_billing';
    const KEY_DEFAULT_SHIPPING = 'default_shipping';
    const KEY_ID = 'id';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_REGION = 'region';
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
    /**#@-*/

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
