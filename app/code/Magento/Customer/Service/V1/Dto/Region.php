<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class Region
 */
class Region extends \Magento\Service\Entity\AbstractDto
{
    /**#@+
     * Array keys
     */
    const KEY_REGION_CODE = 'region_code';
    const KEY_REGION = 'region';
    const KEY_REGION_ID = 'region_id';
    /**#@-*/

    /**
     * Get region code
     *
     * @return string
     */
    public function getRegionCode()
    {
        return $this->_get(self::KEY_REGION_CODE);
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->_get(self::KEY_REGION);
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
}
