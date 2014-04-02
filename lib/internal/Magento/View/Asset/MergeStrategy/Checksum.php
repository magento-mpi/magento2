<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

/**
 * Skip merging if all of the files that need to be merged were not modified
 *
 * Each file will be resolved and its mtime will be checked.
 * Then combination of all mtimes will be compared to a special .dat file that contains mtimes from previous merging
 */
class Checksum implements \Magento\View\Asset\MergeStrategyInterface
{
    /**
     * @var \Magento\View\Asset\MergeStrategyInterface
     */
    protected $strategy;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\View\Asset\MergeStrategyInterface $strategy
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\View\Asset\MergeStrategyInterface $strategy,
        \Magento\App\Filesystem $filesystem
    ) {
        $this->strategy = $strategy;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $assetsToMerge, \Magento\View\Asset\LocalInterface $resultAsset)
    {
        $sourceDir = $dir = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $mTime = null;
        /** @var \Magento\View\Asset\MergeableInterface $asset */
        foreach ($assetsToMerge as $asset) {
            $mTime .= $sourceDir->stat($sourceDir->getRelativePath($asset->getSourceFile()))['mtime'];
        }
        if (null === $mTime) {
            return; // nothing to merge
        }

        $dat = $resultAsset->getRelativePath() . '.dat';
        $targetDir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        if (!$targetDir->isExist($dat) || strcmp($mTime, $targetDir->readFile($dat)) !== 0) {
            $this->strategy->merge($assetsToMerge, $resultAsset);
            $targetDir->writeFile($dat, $mTime);
        }
    }
}
