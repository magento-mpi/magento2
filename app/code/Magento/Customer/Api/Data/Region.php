<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Customer address region interface.
 */
interface Region extends ExtensibleDataInterface
{
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
