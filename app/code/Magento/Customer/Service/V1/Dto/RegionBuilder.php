<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class RegionBuilder
 */
class RegionBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Set region code
     *
     * @param string $regionCode
     * @return $this
     */
    public function setRegionCode($regionCode)
    {
        $this->_data[Region::KEY_REGION_CODE] = $regionCode;
        return $this;
    }

    /**
     * Set region
     *
     * @param string $regionName
     * @return $this
     */
    public function setRegion($regionName)
    {
        $this->_data[Region::KEY_REGION] = $regionName;
        return $this;
    }

    /**
     * Set region id
     *
     * @param string $regionId
     * @return $this
     */
    public function setRegionId($regionId)
    {
        $this->_data[Region::KEY_REGION_ID] = $regionId;
        return $this;
    }
}
