<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Data;

/**
 * Data Model implementing Address Region interface
 */
class Region extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Magento\Customer\Api\Data\RegionInterface
{
    /**
     * Get region code
     *
     * @return string
     */
    public function getRegionCode()
    {
        return $this->_get(self::REGION_CODE);
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->_get(self::REGION);
    }

    /**
     * Get region id
     *
     * @return int
     */
    public function getRegionId()
    {
        return $this->_get(self::REGION_ID);
    }
}
