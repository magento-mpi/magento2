<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * An interface for getting source file for an asset
 */
interface SourceFileInterface
{
    /**
     * Obtain source file of the specified asset
     *
     * Returns false if file resolution failed
     *
     * @param LocalInterface $asset
     * @return string|bool
     */
    public function getSourceFile(LocalInterface $asset);
} 
