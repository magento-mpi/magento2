<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * An interface for "preprocessing" asset contents
 */
interface PreProcessorInterface
{
    /**
     * Transform content and/or content type for the specified preprocessing chain object
     *
     * @param \Magento\View\Asset\PreProcessor\Chain $chain
     */
    public function process(PreProcessor\Chain $chain);
} 
