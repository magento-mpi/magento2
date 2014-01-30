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
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set('id', (string)$id);
    }

    /**
     * @param boolean $defaultShipping
     * @return $this
     */
    public function setDefaultShipping($defaultShipping)
    {
        return $this->_set('default_shipping', (bool)$defaultShipping);
    }

    /**
     * @param boolean $defaultBilling
     * @return $this
     */
    public function setDefaultBilling($defaultBilling)
    {
        return $this->_set('default_billing', (bool)$defaultBilling);
    }

    /**
     * @param string[] $data
     * @return $this
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

        if (isset($region) && !isset($data['region'])) {
            $this->setRegion(new Region($region));
        }

        return $this;
    }

    /**
     * @param Region $region
     * @return $this
     */
    public function setRegion(Region $region)
    {
        return $this->_set('region', $region);
    }

    /**
     * @param int $countryId
     * @return $this
     */
    public function setCountryId($countryId)
    {
        return $this->_set('country_id', $countryId);
    }

    /**
     * @param \string[] $street
     * @return $this
     */
    public function setStreet($street)
    {
        return $this->_set('street', $street);
    }

    /**
     * @param string $company
     * @return $this
     */
    public function setCompany($company)
    {
        return $this->_set('company', $company);
    }

    /**
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone)
    {
        return $this->_set('telephone', $telephone);
    }

    /**
     * @param string $fax
     * @return $this
     */
    public function setFax($fax)
    {
        return $this->_set('fax', $fax);
    }

    /**
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode)
    {
        return $this->_set('postcode', $postcode);
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        return $this->_set('city', $city);
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        return $this->_set('firstname', $firstname);
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        return $this->_set('lastname', $lastname);
    }

    /**
     * @param string $middlename
     * @return $this
     */
    public function setMiddlename($middlename)
    {
        return $this->_set('middlename', $middlename);
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        return $this->_set('prefix', $prefix);
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        return $this->_set('suffix', $suffix);
    }

    /**
     * @param string $vatId
     * @return $this
     */
    public function setVatId($vatId)
    {
        return $this->_set('vat_id', $vatId);
    }

    /**
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        /** XXX: (string) Needed for tests to pass */
        return $this->_set('customer_id', (string)$customerId);
    }
}
