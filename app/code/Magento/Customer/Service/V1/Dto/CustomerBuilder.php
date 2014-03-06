<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class CustomerBuilder
 */
class CustomerBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Set confirmation
     *
     * @param string $confirmation
     * @return $this
     */
    public function setConfirmation($confirmation)
    {
        return $this->_set(Customer::CONFIRMATION, $confirmation);
    }

    /**
     * Set created time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->_set(Customer::CREATED_AT, $createdAt);
    }

    /**
     * Set created area
     *
     * @param string $createdIn
     * @return $this
     */
    public function setCreatedIn($createdIn)
    {
        return $this->_set(Customer::CREATED_IN, $createdIn);
    }

    /**
     * Set date of birth
     *
     * @param string $dob
     * @return $this
     */
    public function setDob($dob)
    {
        return $this->_set(Customer::DOB, $dob);
    }

    /**
     * Set email address
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        return $this->_set(Customer::EMAIL, $email);
    }

    /**
     * Set first name
     *
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        return $this->_set(Customer::FIRSTNAME, $firstname);
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return $this
     */
    public function setGender($gender)
    {
        return $this->_set(Customer::GENDER, $gender);
    }

    /**
     * Set group id
     *
     * @param string $groupId
     * @return $this
     */
    public function setGroupId($groupId)
    {
        return $this->_set(Customer::GROUP_ID, $groupId);
    }

    /**
     * Set customer id
     *
     * @param int $id
     * @return $this
     */
    public function setCustomerId($id)
    {
        return $this->_set(Customer::ID, $id);
    }

    /**
     * Set last name
     *
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        return $this->_set(Customer::LASTNAME, $lastname);
    }

    /**
     * Set middle name
     *
     * @param string $middlename
     * @return $this
     */
    public function setMiddlename($middlename)
    {
        return $this->_set(Customer::MIDDLENAME, $middlename);
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        return $this->_set(Customer::PREFIX, $prefix);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->_set(Customer::STORE_ID, $storeId);
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        return $this->_set(Customer::SUFFIX, $suffix);
    }

    /**
     * Set tax Vat
     *
     * @param string $taxvat
     * @return $this
     */
    public function setTaxvat($taxvat)
    {
        return $this->_set(Customer::TAXVAT, $taxvat);
    }

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        return $this->_set(Customer::WEBSITE_ID, $websiteId);
    }

    /**
     * Set Rp token
     *
     * @param string $rpToken
     * @return $this
     */
    public function getRpToken($rpToken)
    {
        return $this->_set(Customer::RP_TOKEN, $rpToken);
    }

    /**
     * Set Rp token created time
     *
     * @param string $rpTokenCreatedAt
     * @return $this
     */
    public function getRpTokenCreatedAt($rpTokenCreatedAt)
    {
        return $this->_set(Customer::RP_TOKEN_CREATED_AT, $rpTokenCreatedAt);
    }

    /**
     * Adding ability to set custom attribute code
     *
     * @param string $attributeCode
     * @param string|int $value
     * @return $this
     * @todo remove it after merged with pull request 417
     */
    public function setAttribute($attributeCode, $value)
    {
        return $this->_set($attributeCode, $value);
    }

    /**
     * Builds the entity.
     *
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function create()
    {
        return parent::create();
    }
}
