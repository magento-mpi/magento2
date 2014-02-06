<?php
/**
 * Address class acts as a DTO for the Customer Service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

use Magento\Customer\Service\V1\Dto\Region;

class Address extends \Magento\Service\Entity\AbstractDto implements Eav\EntityInterface
{
    const ADDRESS_TYPE_BILLING = 'billing';
    const ADDRESS_TYPE_SHIPPING = 'shipping';
    const KEY_COUNTRY_ID = 'country_id';
    const KEY_DEFAULT_BILLING = 'default_billing';
    const KEY_DEFAULT_SHIPPING = 'default_shipping';
    const KEY_ID = 'id';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_REGION = 'region';
    const KEY_REGION_ID = 'region_id';
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

    protected $_validAttributes = [
        self::KEY_COUNTRY_ID,
        self::KEY_DEFAULT_BILLING,
        self::KEY_DEFAULT_SHIPPING,
        self::KEY_ID,
        self::KEY_CUSTOMER_ID,
        self::KEY_REGION,
        self::KEY_REGION_ID,
        self::KEY_STREET,
        self::KEY_COMPANY,
        self::KEY_TELEPHONE,
        self::KEY_FAX,
        self::KEY_POSTCODE,
        self::KEY_CITY,
        self::KEY_FIRSTNAME,
        self::KEY_LASTNAME,
        self::KEY_MIDDLENAME,
        self::KEY_PREFIX,
        self::KEY_SUFFIX,
        self::KEY_VAT_ID
    ];

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
     * Retrieve array of all attributes, in the form of 'attribute code' => <attribute value'
     *
     * @return array
     */
    public function getAttributes()
    {
        $unvalidatedData = $this->__toArray();
        $validData = [];
        foreach ($this->_validAttributes as $attributeCode) {
            if (isset($unvalidatedData[$attributeCode])) {
                $validData[$attributeCode] = $unvalidatedData[$attributeCode];
            }
        }
        /** This triggers some code in _updateAddressModel in CustomerV1 Service */
        if (!is_null($this->getRegion())) {
            $validData['region_id'] = $this->getRegion()->getRegionId();
            $validData['region'] = $this->getRegion()->getRegion();
        }
        return $validData;
    }

    /**
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (isset($attributes[$attributeCode])) {
            return $attributes[$attributeCode];
        }
        return null;
    }

    /**
     * @return Region
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
