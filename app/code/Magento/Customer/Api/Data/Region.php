<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api\Data;

interface Region
{
    /**
     * Get region code
     *
     * @return string
     */
    public function getRegionCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setRegionCode($value);

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion();

    /**
     * @param string $value
     * @return $this
     */
    public function setRegion($value);

    /**
     * Get region id
     *
     * @return int
     */
    public function getRegionId();
} 
