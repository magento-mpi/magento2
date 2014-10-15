<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api\Data;

interface Customer
{
    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get group id
     *
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setGroupId($groupId);

    /**
     * Get default billing address id
     *
     * @return string|null
     */
    public function getDefaultBilling();

    /**
     * Set default billing address id
     *
     * @param int $defaultBilling
     * @return $this
     */
    public function setDefaultBilling($defaultBilling);

    /**
     * Get default shipping address id
     *
     * @return int|null
     */
    public function getDefaultShipping();

    /**
     * Set default shipping address id
     *
     * @param int $defaultShipping
     * @return $this
     */
    public function setDefaultShipping($defaultShipping);

    /**
     * Get confirmation
     *
     * @return string|null
     */
    public function getConfirmation();

    /**
     * Set confirmation
     *
     * @param string $confirmation
     * @return $this
     */
    public function setConfirmation($confirmation);

    /**
     * Get created at time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get created in area
     *
     * @return string|null
     */
    public function getCreatedIn();

    /**
     * Set created in area
     * @param string $createdIn
     * @return $this
     */
    public function setCreatedIn($createdIn);

    /**
     * Get date of birth
     *
     * @return string|null
     */
    public function getDob();

    /**
     * Set date of birth
     *
     * @param string $dob
     * @return $this
     */
    public function setDob($dob);

    /**
     * Get email address
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email address
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);
    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Set first name
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname);

    /**
     * Get gender
     *
     * @return string|null
     */
    public function getGender();

    /**
     * Set gender
     *
     * @param string $gender
     * @return $this
     */
    public function setGender($gender);

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastname();

    /**
     * Set last name
     *
     * @param $lastname
     * @return $this
     */
    public function setLastname($lastname);

    /**
     * Get middle name
     *
     * @return string|null
     */
    public function getMiddlename();

    /**
     * Set middle name
     *
     * @param string $middlename
     * @return $this
     */
    public function setMiddlename($middlename);

    /**
     * Get prefix
     *
     * @return string|null
     */
    public function getPrefix();

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get suffix
     *
     * @return string|null
     */
    public function getSuffix();

    /**
     * Set suffix
     *
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix);

    /**
     * Get tax Vat
     *
     * @return string|null
     */
    public function getTaxvat();

    /**
     * Set tax Vat
     * @param string $taxvat
     * @return $this
     */
    public function setTaxvat($taxvat);

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return \Magento\Customer\Api\Data\Address[]
     */
    public function getAddresses();
}
