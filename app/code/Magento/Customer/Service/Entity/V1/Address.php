<?php
/**
 * Address class acts as a DTO for the Customer Service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

use Magento\Service\Entity\AbstractDto;
use Magento\Service\Entity\LockableLazyArrayClone;

class Address extends AbstractDto implements Eav\EntityInterface
{
    /**
     * @var array
     */
    private $_nonAttributes = ['id', 'customer_id', 'region', 'default_billing', 'default_shipping'];

    public function __construct()
    {
        parent::__construct();
        $this->_data['region'] = new Region();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get('id');
    }

    /**
     * @param int $id
     * @return Address
     */
    public function setId($id)
    {
        return $this->_set('id', $id);
    }

    /**
     * @return boolean|null
     */
    public function isDefaultShipping()
    {
        return $this->_get('default_shipping');
    }

    /**
     * @param boolean $defaultShipping
     * @return Address
     */
    public function setDefaultShipping($defaultShipping)
    {
        return $this->_set('default_shipping', $defaultShipping);
    }

    /**
     * @return boolean|null
     */
    public function isDefaultBilling()
    {
        return $this->_get('default_billing');
    }

    /**
     * @param boolean $defaultBilling
     * @return Address
     */
    public function setDefaultBilling($defaultBilling)
    {
        return $this->_set('default_billing', $defaultBilling);
    }

    /**
     * @return string[]
     */
    public function getAttributes()
    {
        $attributes = $this->_data->__toArray();
        foreach ($this->_nonAttributes as $keyName) {
            unset ($attributes[$keyName]);
        }
        $attributes['region_id'] = $this->getRegion()->getRegionId();
        $attributes['region'] = $this->getRegion()->getRegion();
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
            && !in_array($attributeCode, $this->_nonAttributes)) {
            return $attributes[$attributeCode];
        }
        return null;
    }

    /**
     * @param string[] $attributes
     * @return Address
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    /**
     * @param string $attributeCode
     * @param string $attributeValue
     * @return $this
     */
    public function setAttribute($attributeCode, $attributeValue)
    {
        $this->_data[$attributeCode] = $attributeValue;
        return $this;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->_get('region', new Region());
    }

    /**
     * @param Region $region
     * @return Address
     */
    public function setRegion(Region $region)
    {
        return $this->_set('region', $region);
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->_get('country_id');
    }

    /**
     * @param int $countryId
     * @return Address
     */
    public function setCountryId($countryId)
    {
        return $this->_set('country_id', $countryId);
    }

    /**
     * @return \string[]|null
     */
    public function getStreet()
    {
        return $this->_get('street');
    }

    /**
     * @param \string[] $street
     * @return Address
     */
    public function setStreet($street)
    {
        return $this->_set('street', $street);
    }

    /**
     * @return string|null
     */
    public function getCompany()
    {
        return $this->_get('company');
    }

    /**
     * @param string $company
     * @return Address
     */
    public function setCompany($company)
    {
        return $this->_set('company', $company);
    }

    /**
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_get('telephone');
    }

    /**
     * @param string $telephone
     * @return Address
     */
    public function setTelephone($telephone)
    {
        return $this->_set('telephone', $telephone);
    }

    /**
     * @return string|null
     */
    public function getFax()
    {
        return $this->_get('fax');
    }

    /**
     * @param string $fax
     * @return Address
     */
    public function setFax($fax)
    {
        return $this->_set('fax', $fax);
    }

    /**
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->_get('postcode');
    }

    /**
     * @param string $postcode
     * @return Address
     */
    public function setPostcode($postcode)
    {
        return $this->_set('postcode', $postcode);
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get('city');
    }

    /**
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        return $this->_set('city', $city);
    }

    /**
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->_get('firstname');
    }

    /**
     * @param string $firstname
     * @return Address
     */
    public function setFirstname($firstname)
    {
        return $this->_set('firstname', $firstname);
    }

    /**
     * @return string|null
     */
    public function getLastname()
    {
        return $this->_get('lastname');
    }

    /**
     * @param string $lastname
     * @return Address
     */
    public function setLastname($lastname)
    {
        return $this->_set('lastname', $lastname);
    }

    /**
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->_get('middlename');
    }

    /**
     * @param string $middlename
     * @return Address
     */
    public function setMiddlename($middlename)
    {
        return $this->_set('middlename', $middlename);
    }

    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->_get('prefix');
    }

    /**
     * @param string $prefix
     * @return Address
     */
    public function setPrefix($prefix)
    {
        return $this->_set('prefix', $prefix);
    }

    /**
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->_get('suffix');
    }

    /**
     * @param string $suffix
     * @return Address
     */
    public function setSuffix($suffix)
    {
        return $this->_set('suffix', $suffix);
    }

    /**
     * @return string|null
     */
    public function getVatId()
    {
        return $this->_get('vat_id');
    }

    /**
     * @param string $vatId
     * @return Address
     */
    public function setVatId($vatId)
    {
        return $this->_set('vat_id', $vatId);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get('customer_id');
    }

    /**
     * @param string $customerId
     * @return Address
     */
    public function setCustomerId($customerId)
    {
        return $this->_set('customer_id', $customerId);
    }
}
