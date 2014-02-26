<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Interface for different file merging strategies
 */
interface MergeStrategyInterface
{
    /**
     * Merge files into one and save to disk
     *
     * @param array $publicFiles List of full file paths to merge
     * @param string $destinationFile Full file path for merged file
     * @param string $contentType Asset content type
     * @return void
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType);
}
