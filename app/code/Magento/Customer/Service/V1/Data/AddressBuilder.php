<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Service\Data\EAV\AbstractObjectBuilder;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

/**
 * Address class acts as a Service Data Object for the Customer Service
 *
 * @method Address create()
 */
class AddressBuilder extends AbstractObjectBuilder
{
    /** @var RegionBuilder */
    protected $_regionBuilder;

    /** @var CustomerMetadataServiceInterface */
    protected $_metadataService;

    /**
     * Initialize dependencies.
     *
     * @param RegionBuilder $regionBuilder
     * @param CustomerMetadataServiceInterface $metadataService
     */
    public function __construct(RegionBuilder $regionBuilder, CustomerMetadataServiceInterface $metadataService)
    {
        parent::__construct();
        $this->_metadataService = $metadataService;
        $this->_regionBuilder = $regionBuilder;
        $this->_data[Address::KEY_REGION] = $regionBuilder->create();
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set(Address::KEY_ID, $id);
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
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(Address::KEY_REGION, $data)) {
            if (!is_array($data[Address::KEY_REGION])) {
                // Region data has been submitted as individual keys of Address object. Let's extract it.
                $regionData = [];
                foreach ([Region::KEY_REGION, Region::KEY_REGION_CODE, Region::KEY_REGION_ID] as $attrCode) {
                    if (isset($data[$attrCode])) {
                        $regionData[$attrCode] = $data[$attrCode];
                    }
                }
            } else {
                $regionData = $data[Address::KEY_REGION];
            }
            $data[Address::KEY_REGION] = $this->_regionBuilder->populateWithArray($regionData)->create();
        }
        return parent::_setDataValues($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesCodes()
    {
        $attributeCodes = [];
        foreach ($this->_metadataService->getCustomAddressAttributeMetadata() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
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
        return $this->_set(Address::KEY_CUSTOMER_ID, $customerId);
    }
}
