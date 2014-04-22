<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset;

/**
 * Interface for merging multiple assets into one
 */
interface MergeStrategyInterface
{
    /**
     * Merge assets into one
     *
     * The $resultAsset may be used to persist result
     *
     * @param MergeableInterface[] $assetsToMerge
     * @param LocalInterface $resultAsset
     */
    public function merge(array $assetsToMerge, LocalInterface $resultAsset);
}
