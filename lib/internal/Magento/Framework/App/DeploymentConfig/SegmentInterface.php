<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

/**
 * An abstraction for deployment configuration "segments"
 */
interface SegmentInterface
{
    /**
     * Gets segment key of deployment configuration
     *
     * @return string
     */
    public function getKey();

    /**
     * Gets the segment data
     *
     * @return array
     */
    public function getData();
}
