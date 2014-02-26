<?php
/**
 * Class Customer. Uses array to hold data, setters return $this so they can be chained.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

class CustomerBuilder extends \Magento\Service\Data\EAV\AbstractObjectBuilder
{
    /** @var CustomerMetadataServiceInterface */
    protected $_metadataService;

    /**
     * Initialize dependencies.
     *
     * @param CustomerMetadataServiceInterface $metadataService
     */
    public function __construct(CustomerMetadataServiceInterface $metadataService)
    {
        parent::__construct();
        $this->_metadataService = $metadataService;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesCodes()
    {
        $attributeCodes = [];
        foreach ($this->_metadataService->getCustomCustomerAttributeMetadata() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
    }

    /**
     * @param string $confirmation
     * @return CustomerBuilder
     */
    public function setConfirmation($confirmation)
    {
        return $this->_set(Customer::CONFIRMATION, $confirmation);
    }

    /**
     * @param string $createdAt
     * @return CustomerBuilder
     */
    public function setCreatedAt($createdAt)
    {
        return $this->_set(Customer::CREATED_AT, $createdAt);
    }

    /**
     * @param string $createdIn
     * @return CustomerBuilder
     */
    public function setCreatedIn($createdIn)
    {
        return $this->_set(Customer::CREATED_IN, $createdIn);
    }

    /**
     * @param string $dob
     * @return CustomerBuilder
     */
    public function setDob($dob)
    {
        return $this->_set(Customer::DOB, $dob);
    }

    /**
     * @param string $email
     * @return CustomerBuilder
     */
    public function setEmail($email)
    {
        return $this->_set(Customer::EMAIL, $email);
    }

    /**
     * @param string $firstname
     * @return CustomerBuilder
     */
    public function setFirstname($firstname)
    {
        return $this->_set(Customer::FIRSTNAME, $firstname);
    }

    /**
     * @param string $gender
     * @return CustomerBuilder
     */
    public function setGender($gender)
    {
        return $this->_set(Customer::GENDER, $gender);
    }

    /**
     * @param string $groupId
     * @return CustomerBuilder
     */
    public function setGroupId($groupId)
    {
        return $this->_set(Customer::GROUP_ID, $groupId);
    }

    /**
     * @param int $id
     * @return CustomerBuilder
     */
    public function setId($id)
    {
        return $this->_set(Customer::ID, $id);
    }

    /**
     * @param string $lastname
     * @return CustomerBuilder
     */
    public function setLastname($lastname)
    {
        return $this->_set(Customer::LASTNAME, $lastname);
    }

    /**
     * @param string $middlename
     * @return CustomerBuilder
     */
    public function setMiddlename($middlename)
    {
        return $this->_set(Customer::MIDDLENAME, $middlename);
    }

    /**
     * @param string $prefix
     * @return CustomerBuilder
     */
    public function setPrefix($prefix)
    {
        return $this->_set(Customer::PREFIX, $prefix);
    }

    /**
     * @param int $storeId
     * @return CustomerBuilder
     */
    public function setStoreId($storeId)
    {
        return $this->_set(Customer::STORE_ID, $storeId);
    }

    /**
     * @param string $suffix
     * @return CustomerBuilder
     */
    public function setSuffix($suffix)
    {
        return $this->_set(Customer::SUFFIX, $suffix);
    }

    /**
     * @param string $taxvat
     * @return CustomerBuilder
     */
    public function setTaxvat($taxvat)
    {
        return $this->_set(Customer::TAXVAT, $taxvat);
    }

    /**
     * @param int $websiteId
     * @return CustomerBuilder
     */
    public function setWebsiteId($websiteId)
    {
        return $this->_set(Customer::WEBSITE_ID, $websiteId);
    }
}
