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

/**
 * Address class acts as a SDO for Customer Services
 */
class Address extends \Magento\Service\Entity\AbstractDto implements Eav\EntityInterface
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
    const KEY_REGION_CODE = Region::KEY_REGION_CODE;
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
     * Valid attribute name
     *
     * @var string[]
     */
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
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::KEY_ID);
    }

    /**
     * Get if this address is default shipping address.
     *
     * @return bool|null
     */
    public function isDefaultShipping()
    {
        return $this->_get(self::KEY_DEFAULT_SHIPPING);
    }

    /**
     * Get if this address is default billing address
     *
     * @return bool|null
     */
    public function isDefaultBilling()
    {
        return $this->_get(self::KEY_DEFAULT_BILLING);
    }

    /**
     * Retrieve array of all attributes, in the form of 'attribute code' => <attribute value'>.
     *
     * @return string[] attributes, in the form of 'attribute code' => <attribute value'>
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
            $region = $this->getRegion();
            if (!is_null($region->getRegionId())) {
                $validData[self::KEY_REGION_ID] = $region->getRegionId();
            } else {
                unset($validData[self::KEY_REGION_ID]);
            }
            if (!is_null($region->getRegion())) {
                $validData[self::KEY_REGION] = $region->getRegion();
            } else {
                unset($validData[self::KEY_REGION]);
            }
            if (!is_null($region->getRegionCode())) {
                $validData[self::KEY_REGION_CODE] = $region->getRegionCode();
            } else {
                unset($validData[self::KEY_REGION_CODE]);
            }
        } else {
            unset($validData[self::KEY_REGION]);
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
        $attributes = $this->getAttributes();
        if (isset($attributes[$attributeCode])) {
            return $attributes[$attributeCode];
        }
        return null;
    }

    /**
     * Get region
     *
     * @return \Magento\Customer\Service\V1\Dto\Region|null
     */
    public function getRegion()
    {
        return $this->_get(self::KEY_REGION);
    }

    /**
     * Get country id
     *
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->_get(self::KEY_COUNTRY_ID);
    }

    /**
     * Get street
     *
     * @return string[]|null
     */
    public function getStreet()
    {
        return $this->_get(self::KEY_STREET);
    }

    /**
     * Get company
     *
     * @return string|null
     */
    public function getCompany()
    {
        return $this->_get(self::KEY_COMPANY);
    }

    /**
     * Get telephone number
     *
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_get(self::KEY_TELEPHONE);
    }

    /**
     * Get fax number
     *
     * @return string|null
     */
    public function getFax()
    {
        return $this->_get(self::KEY_FAX);
    }

    /**
     * Get postcode
     *
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->_get(self::KEY_POSTCODE);
    }

    /**
     * Get city name
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::KEY_CITY);
    }

    /**
     * Get first name
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->_get(self::KEY_FIRSTNAME);
    }

    /**
     * Get last name
     *
     * @return string|null
     */
    public function getLastname()
    {
        return $this->_get(self::KEY_LASTNAME);
    }

    /**
     * Get middle name
     *
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->_get(self::KEY_MIDDLENAME);
    }

    /**
     * Get prefix
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->_get(self::KEY_PREFIX);
    }

    /**
     * Get suffix
     *
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->_get(self::KEY_SUFFIX);
    }

    /**
     * Get Vat id
     *
     * @return string|null
     */
    public function getVatId()
    {
        return $this->_get(self::KEY_VAT_ID);
    }

    /**
     * Get customer id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::KEY_CUSTOMER_ID);
    }
}
