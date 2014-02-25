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

class Address extends \Magento\Service\Entity\AbstractDto implements Eav\EntityInterface
{

    const KEY_COUNTRY_ID = 'country_id';

    /**
     * @var array
     */
    private static $_nonAttributes = ['id', 'customer_id', 'default_billing', 'default_shipping'];

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get('id');
    }

    /**
     * @return boolean|null
     */
    public function isDefaultShipping()
    {
        return $this->_get('default_shipping');
    }

    /**
     * @return boolean|null
     */
    public function isDefaultBilling()
    {
        return $this->_get('default_billing');
    }

    /**
     * Get Customer attributes and values.
     *
     * @return array Attribute code is used as key, and attribute value as value.
     */
    public function getAttributes()
    {
        $attributes = $this->_data;
        foreach (self::$_nonAttributes as $keyName) {
            unset($attributes[$keyName]);
        }

        /** This triggers some code in _updateAddressModel in CustomerV1 Service */
        if (!is_null($this->getRegion())) {
            $region = $this->getRegion();
            if (!is_null($region->getRegionId())) {
                $attributes['region_id'] = $region->getRegionId();
            } else {
                unset($attributes['region_id']);
            }
            if (!is_null($region->getRegion())) {
                $attributes['region'] = $region->getRegion();
            } else {
                unset($attributes['region']);
            }
            if (!is_null($region->getRegionCode())) {
                $attributes['region_code'] = $region->getRegionCode();
            } else {
                unset($attributes['region_code']);
            }
        } else {
            unset($attributes['region']);
        }

        return $attributes;
    }

    /**
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (isset($attributes[$attributeCode])
            && !in_array($attributeCode, self::$_nonAttributes)) {
            return $attributes[$attributeCode];
        }
        return null;
    }

    /**
     * @return Region|null
     */
    public function getRegion()
    {
        return $this->_get('region');
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->_get(self::KEY_COUNTRY_ID);
    }

    /**
     * @return string[]|null
     */
    public function getStreet()
    {
        return $this->_get('street');
    }

    /**
     * @return string|null
     */
    public function getCompany()
    {
        return $this->_get('company');
    }

    /**
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_get('telephone');
    }

    /**
     * @return string|null
     */
    public function getFax()
    {
        return $this->_get('fax');
    }

    /**
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->_get('postcode');
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get('city');
    }

    /**
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->_get('firstname');
    }

    /**
     * @return string|null
     */
    public function getLastname()
    {
        return $this->_get('lastname');
    }

    /**
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->_get('middlename');
    }

    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->_get('prefix');
    }

    /**
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->_get('suffix');
    }

    /**
     * @return string|null
     */
    public function getVatId()
    {
        return $this->_get('vat_id');
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get('customer_id');
    }
}
