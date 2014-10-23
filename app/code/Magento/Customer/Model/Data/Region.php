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
class Region extends \Magento\Framework\Service\Data\AbstractExtensibleObject
    implements \Magento\Customer\Api\Data\RegionInterface
{
    /**#@+
     * Array keys
     */
    const REGION_CODE = 'region_code';

    const REGION = 'region';

    const REGION_ID = 'region_id';

    /**#@-*/

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
