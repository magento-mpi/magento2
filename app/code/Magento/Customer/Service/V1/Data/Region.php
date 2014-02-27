<?php
/**
 * Class Region
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

/**
 * Data Object for Address Region
 */
class Region extends \Magento\Service\Data\AbstractObject
{
    /**#@+
     * Array keys
     */
    const KEY_REGION_CODE = 'region_code';
    const KEY_REGION = 'region';
    const KEY_REGION_ID = 'region_id';
    /**#@-*/

    /**
     * @return string
     */
    public function getRegionCode()
    {
        return $this->_get(self::KEY_REGION_CODE);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->_get(self::KEY_REGION);
    }

    /**
     * @return int
     */
    public function getRegionId()
    {
        return $this->_get(self::KEY_REGION_ID);
    }
}
