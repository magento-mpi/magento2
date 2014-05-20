<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

class TaxRate extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_ID = 'id';

    const KEY_COUNTRY_ID = 'country_id';

    const KEY_REGION_ID = 'region_id';

    const KEY_ZIP = 'zip';

    const KEY_ZIP_RANGE = 'zip_range';

    const KEY_PERCENTAGE_RATE = 'percentage_rate';
    /**#@-*/

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::KEY_ID);
    }

    /**
     * Get country id
     *
     * @return string
     */
    public function getCountryId()
    {
        return $this->_get(self::KEY_COUNTRY_ID);
    }

    /**
     * Get region id
     *
     * @return int
     */
    public function getRegionId()
    {
        return $this->_get(self::KEY_REGION_ID);
    }

    /**
     * Get zip
     *
     * @return string|null
     */
    public function getZip()
    {
        return $this->_get(self::KEY_ZIP);
    }

    /**
     * Get zip range
     *
     * @return ZipRange|null
     */
    public function getZipRange()
    {
        return $this->_get(self::KEY_ZIP_RANGE);
    }

    /**
     * Get tax rate in percentage
     *
     * @return float
     */
    public function getPercentageRate()
    {
        return $this->_get(self::KEY_PERCENTAGE_RATE);
    }
}
