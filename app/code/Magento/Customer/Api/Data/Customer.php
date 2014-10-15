<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Customer interface.
 */
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
     * Get default billing address id
     *
     * @return string|null
     */
    public function getDefaultBilling();

    /**
     * Get default shipping address id
     *
     * @return int|null
     */
    public function getDefaultShipping();

    /**
     * Get confirmation
     *
     * @return string|null
     */
    public function getConfirmation();

    /**
     * Get created at time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get created in area
     *
     * @return string|null
     */
    public function getCreatedIn();

    /**
     * Get date of birth
     *
     * @return string|null
     */
    public function getDob();

    /**
     * Get email address
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Get gender
     *
     * @return string|null
     */
    public function getGender();

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastname();

    /**
     * Get middle name
     *
     * @return string|null
     */
    public function getMiddlename();

    /**
     * Get prefix
     *
     * @return string|null
     */
    public function getPrefix();

    /**
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Get suffix
     *
     * @return string|null
     */
    public function getSuffix();

    /**
     * Get tax Vat
     *
     * @return string|null
     */
    public function getTaxvat();

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Get customer addresses.
     *
     * @return \Magento\Customer\Api\Data\Address[]
     */
    public function getAddresses();
}
