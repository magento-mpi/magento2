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
    /**
     * @var array
     */
    private static $_nonAttributes = ['id', 'customer_id', 'region', 'default_billing', 'default_shipping'];

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
     * @return string[]
     */
    public function getAttributes()
    {
        $attributes = $this->_data;
        foreach (self::$_nonAttributes as $keyName) {
            unset ($attributes[$keyName]);
        }

        /** This triggers some code in _updateAddressModel in CustomerV1 Service */
        if (!is_null($this->getRegion())) {
            $attributes['region_id'] = $this->getRegion()->getRegionId();

            $attributes['region'] = $this->getRegion()->getRegion();
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
     * @return Region
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
        return $this->_get('country_id');
    }

    /**
     * @return \string[]|null
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
