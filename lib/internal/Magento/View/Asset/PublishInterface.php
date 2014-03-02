<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * An interface for publishing local assets
 */
interface PublishInterface
{
    /**
     * Publish the asset and return true if it is public
     *
     * @param LocalInterface $asset
     * @return bool
     */
    public function publish(LocalInterface $asset);
} 
