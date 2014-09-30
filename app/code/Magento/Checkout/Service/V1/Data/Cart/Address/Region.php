<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart\Address;

/**
 * @codeCoverageIgnore
 */
class Region extends \Magento\Framework\Service\Data\AbstractExtensibleObject
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
