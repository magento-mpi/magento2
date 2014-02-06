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
 * @method Address create()
 */
class AddressBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    protected $_regionBuilder;

    /**
     * @param \Magento\Customer\Service\V1\Dto\RegionBuilder $regionBuilder
     */
    public function __construct(RegionBuilder $regionBuilder)
    {
        parent::__construct();
        $this->_regionBuilder = $regionBuilder;
        $this->_data['region'] = $regionBuilder->create();
    }

    /**
     * @param int $id
     * @return AddressBuilder
     */
    public function setId($id)
    {
        return $this->_set('id', (string)$id);
    }

    /**
     * @param boolean $defaultShipping
     * @return AddressBuilder
     */
    public function setDefaultShipping($defaultShipping)
    {
        return $this->_set(Address::IS_DEFAULT_SHIPPING, (bool)$defaultShipping);
    }

    /**
     * @param boolean $defaultBilling
     * @return AddressBuilder
     */
    public function setDefaultBilling($defaultBilling)
    {
        return $this->_set(Address::IS_DEFAULT_BILLING, (bool)$defaultBilling);
    }

    /**
     * @param string[] $data
     * @return AddressBuilder
     */
    public function populateWithArray(array $data)
    {
        unset($data['region_id']);
        if (isset($data['region'])) {
            $region = $data['region'];
            if (!($region instanceof Region)) {
                unset($data['region']);
            }
        }

        parent::populateWithArray($data);

        return $this;
    }

    /**
     * @param Region $region
     * @return AddressBuilder
     */
    public function setRegion(Region $region)
    {
        return $this->_set('region', $region);
    }

    /**
     * @param int $countryId
     * @return AddressBuilder
     */
    public function setCountryId($countryId)
    {
        return $this->_set('country_id', $countryId);
    }

    /**
     * @param \string[] $street
     * @return AddressBuilder
     */
    public function setStreet($street)
    {
        return $this->_set('street', $street);
    }

    /**
     * @param string $company
     * @return AddressBuilder
     */
    public function setCompany($company)
    {
        return $this->_set('company', $company);
    }

    /**
     * @param string $telephone
     * @return AddressBuilder
     */
    public function setTelephone($telephone)
    {
        return $this->_set('telephone', $telephone);
    }

    /**
     * @param string $fax
     * @return AddressBuilder
     */
    public function setFax($fax)
    {
        return $this->_set('fax', $fax);
    }

    /**
     * @param string $postcode
     * @return AddressBuilder
     */
    public function setPostcode($postcode)
    {
        return $this->_set('postcode', $postcode);
    }

    /**
     * @param string $city
     * @return AddressBuilder
     */
    public function setCity($city)
    {
        return $this->_set('city', $city);
    }

    /**
     * @param string $firstname
     * @return AddressBuilder
     */
    public function setFirstname($firstname)
    {
        return $this->_set('firstname', $firstname);
    }

    /**
     * @param string $lastname
     * @return AddressBuilder
     */
    public function setLastname($lastname)
    {
        return $this->_set('lastname', $lastname);
    }

    /**
     * @param string $middlename
     * @return AddressBuilder
     */
    public function setMiddlename($middlename)
    {
        return $this->_set('middlename', $middlename);
    }

    /**
     * @param string $prefix
     * @return AddressBuilder
     */
    public function setPrefix($prefix)
    {
        return $this->_set('prefix', $prefix);
    }

    /**
     * @param string $suffix
     * @return AddressBuilder
     */
    public function setSuffix($suffix)
    {
        return $this->_set('suffix', $suffix);
    }

    /**
     * @param string $vatId
     * @return AddressBuilder
     */
    public function setVatId($vatId)
    {
        return $this->_set('vat_id', $vatId);
    }

    /**
     * @param string $customerId
     * @return AddressBuilder
     */
    public function setCustomerId($customerId)
    {
        /** XXX: (string) Needed for tests to pass */
        return $this->_set('customer_id', (string)$customerId);
    }
}
