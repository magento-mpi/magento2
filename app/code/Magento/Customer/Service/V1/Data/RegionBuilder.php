<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

/**
 * Customer Service Address Interface
 *
 * @method Region create()
 */
class RegionBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * @param string $regionCode
     * @return RegionBuilder
     */
    public function setRegionCode($regionCode)
    {
        $this->_data[Region::KEY_REGION_CODE] = $regionCode;
        return $this;
    }

    /**
     * @param string $regionName
     * @return RegionBuilder
     */
    public function setRegion($regionName)
    {
        $this->_data[Region::KEY_REGION] = $regionName;
        return $this;
    }

    /**
     * @param string $regionId
     * @return RegionBuilder
     */
    public function setRegionId($regionId)
    {
        $this->_data[Region::KEY_REGION_ID] = $regionId;
        return $this;
    }
}
