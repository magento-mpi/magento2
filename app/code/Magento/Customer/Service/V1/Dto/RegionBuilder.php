<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

class RegionBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param string $regionCode
     * @return RegionBuilder
     */
    public function setRegionCode($regionCode)
    {
        $this->_data['region_code'] = $regionCode;
        return $this;
    }

    /**
     * @param string $regionName
     * @return RegionBuilder
     */
    public function setRegion($regionName)
    {
        $this->_data['region'] = $regionName;
        return $this;
    }

    /**
     * @param string $regionId
     * @return RegionBuilder
     */
    public function setRegionId($regionId)
    {
        $this->_data['region_id'] = $regionId;
        return $this;
    }
}
