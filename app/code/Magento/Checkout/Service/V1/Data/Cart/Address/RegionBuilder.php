<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart\Address;

/**
 * Builder for the Region Service Data Object
 *
 * @method Region create()
 */
class RegionBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
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
