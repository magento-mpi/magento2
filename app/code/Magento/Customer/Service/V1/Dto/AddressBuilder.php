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
    /** @var RegionBuilder */
    protected $_regionBuilder;

    /**
     * @param RegionBuilder $regionBuilder
     */
    public function __construct(RegionBuilder $regionBuilder)
    {
        parent::__construct();
        $this->_regionBuilder = $regionBuilder;
        $this->_data[Address::KEY_REGION] = $regionBuilder->create();
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set(Address::KEY_ID, (string)$id);
    }

    /**
     * @param boolean $defaultShipping
     * @return $this
     */
    public function setDefaultShipping($defaultShipping)
    {
        return $this->_set(Address::KEY_DEFAULT_SHIPPING, (bool)$defaultShipping);
    }

    /**
     * @param boolean $defaultBilling
     * @return $this
     */
    public function setDefaultBilling($defaultBilling)
    {
        return $this->_set(Address::KEY_DEFAULT_BILLING, (bool)$defaultBilling);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function populateWithArray(array $data)
    {
        if (isset($data[Address::KEY_REGION])) {
            $regionData = $data[Address::KEY_REGION];

            if (!is_array($regionData)) {
                throw new \InvalidArgumentException("'region' expected to be an array");
            }

            $data[Address::KEY_REGION] = $this->_regionBuilder->populateWithArray($regionData)->create();
        }

        return parent::populateWithArray($data);
    }

    /**
     * @param Region $region
     * @return $this
     */
    public function setRegion(Region $region)
    {
        return $this->_set(Address::KEY_REGION, $region);
    }

    /**
     * @param int $countryId
     * @return $this
     */
    public function setCountryId($countryId)
    {
        return $this->_set(Address::KEY_COUNTRY_ID, $countryId);
    }

    /**
     * @param \string[] $street
     * @return $this
     */
    public function setStreet($street)
    {
        return $this->_set(Address::KEY_STREET, $street);
    }

    /**
     * @param string $company
     * @return $this
     */
    public function setCompany($company)
    {
        return $this->_set(Address::KEY_COMPANY, $company);
    }

    /**
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone)
    {
        return $this->_set(Address::KEY_TELEPHONE, $telephone);
    }

    /**
     * @param string $fax
     * @return $this
     */
    public function setFax($fax)
    {
        return $this->_set(Address::KEY_FAX, $fax);
    }

    /**
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode)
    {
        return $this->_set(Address::KEY_POSTCODE, $postcode);
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        return $this->_set(Address::KEY_CITY, $city);
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        return $this->_set(Address::KEY_FIRSTNAME, $firstname);
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        return $this->_set(Address::KEY_LASTNAME, $lastname);
    }

    /**
     * @param string $middlename
     * @return $this
     */
    public function setMiddlename($middlename)
    {
        return $this->_set(Address::KEY_MIDDLENAME, $middlename);
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        return $this->_set(Address::KEY_PREFIX, $prefix);
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        return $this->_set(Address::KEY_SUFFIX, $suffix);
    }

    /**
     * @param string $vatId
     * @return $this
     */
    public function setVatId($vatId)
    {
        return $this->_set(Address::KEY_VAT_ID, $vatId);
    }

    /**
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        /** XXX: (string) Needed for tests to pass */
        return $this->_set(Address::KEY_CUSTOMER_ID, (string)$customerId);
    }
}
