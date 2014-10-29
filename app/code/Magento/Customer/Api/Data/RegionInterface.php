<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api\Data;

use Magento\Framework\Api\Data\ExtensibleDataInterface;

/**
 * Customer address region interface.
 */
interface RegionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array
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
    public function getRegionCode();

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion();

    /**
     * Get region id
     *
     * @return int
     */
    public function getRegionId();
}
