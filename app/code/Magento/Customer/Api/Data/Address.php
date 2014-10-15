<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api\Data;

interface Address
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get region
     *
     * @return \Magento\Customer\Api\Data\Region|null
     */
    public function getRegion();

    /**
     * Get region
     *
     * @return \Magento\Customer\Api\Data\Region|null
     */
    public function setRegion(\Magento\Customer\Api\Data\Region $region);

    /**
     * Get country id
     *
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country id
     *
     * @param $code
     * @return mixed
     */
    public function setCountryId($code);

    /**
     * Get street
     *
     * @return string[]|null
     */
    public function getStreet();

    /**
     * Get company
     *
     * @return string|null
     */
    public function getCompany();

    /**
     * Get telephone number
     *
     * @return string|null
     */
    public function getTelephone();

    /**
     * Get fax number
     *
     * @return string|null
     */
    public function getFax();

    /**
     * Get postcode
     *
     * @return string|null
     */
    public function getPostcode();

    /**
     * Get city name
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Get first name
     *
     * @return string|null
     */
    public function getFirstname();

    /**
     * Get last name
     *
     * @return string|null
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
     * Get suffix
     *
     * @return string|null
     */
    public function getSuffix();

    /**
     * Get Vat id
     *
     * @return string|null
     */
    public function getVatId();
}
