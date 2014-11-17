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
interface TaxRateInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_ID = 'id';
    const KEY_COUNTRY_ID = 'country_id';
    const KEY_REGION_ID = 'region_id';
    const KEY_REGION_NAME = 'region_name';
    const KEY_POSTCODE = 'postcode';
    const KEY_ZIP_RANGE = 'zip_range';
    const KEY_PERCENTAGE_RATE = 'percentage_rate';
    const KEY_CODE = 'code';
    const KEY_TITLES = 'titles';
    /**#@-*/

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get country id
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Get region id
     *
     * @return int|null
     */
    public function getRegionId();

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
    public function getPostcode();

    /**
     * Get zip range
     *
     * @return \Magento\Tax\Api\Data\ZipRangeInterface|null
     */
    public function getZipRange();

    /**
     * Get tax rate in percentage
     *
     * @return float
     */
    public function getPercentageRate();

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
