<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * @see \Magento\Tax\Service\V1\Data\TaxRate
 */
interface TaxRateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_ID = 'tax_calculation_rate_id';
    const KEY_COUNTRY_ID = 'tax_country_id';
    const KEY_REGION_ID = 'tax_region_id';
    const KEY_REGION_NAME = 'region_name';
    const KEY_POSTCODE = 'tax_postcode';
    const KEY_ZIP_RANGE_FROM = 'zip_from';
    const KEY_ZIP_RANGE_TO = 'zip_to';
    const KEY_PERCENTAGE_RATE = 'rate';
    const KEY_CODE = 'code';
    const KEY_TITLES = 'titles';
    /**#@-*/

    /**
     * Get id
     *
     * @return int|null
     */
    public function getTaxCalculationRateId();

    /**
     * Get country id
     *
     * @return string
     */
    public function getTaxCountryId();

    /**
     * Get region id
     *
     * @return int|null
     */
    public function getTaxRegionId();

    /**
     * Get region name
     *
     * @return string|null
     */
    public function getRegionName();

    /**
     * Get postcode
     *
     * @return string|null
     */
    public function getTaxPostcode();

    /**
     * Get zip range from
     *
     * @return int|null
     */
    public function getZipFrom();

    /**
     * Get zip range to
     *
     * @return int|null
     */
    public function getZipTo();

    /**
     * Get tax rate in percentage
     *
     * @return float
     */
    public function getRate();

    /**
     * Get tax rate code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get tax rate titles
     *
     * @return \Magento\Tax\Api\Data\TaxRateTitleInterface[]|null
     */
    public function getTitles();
}
