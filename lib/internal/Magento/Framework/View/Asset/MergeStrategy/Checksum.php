<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset\MergeStrategy;

/**
 * Skip merging if all of the files that need to be merged were not modified
 *
 * Each file will be resolved and its mtime will be checked.
 * Then combination of all mtimes will be compared to a special .dat file that contains mtimes from previous merging
 */
class Checksum implements \Magento\Framework\View\Asset\MergeStrategyInterface
{
    /**
     * @var \Magento\Framework\View\Asset\MergeStrategyInterface
     */
    protected $strategy;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Framework\View\Asset\MergeStrategyInterface $strategy
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\View\Asset\MergeStrategyInterface $strategy,
        \Magento\Framework\App\Filesystem $filesystem
    ) {
        $this->strategy = $strategy;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $assetsToMerge, \Magento\Framework\View\Asset\LocalInterface $resultAsset)
    {
        $sourceDir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $mTime = null;
        /** @var \Magento\Framework\View\Asset\MergeableInterface $asset */
        foreach ($assetsToMerge as $asset) {
            $mTime .= $sourceDir->stat($sourceDir->getRelativePath($asset->getSourceFile()))['mtime'];
        }
        if (null === $mTime) {
            return; // nothing to merge
        }

        $dat = $resultAsset->getPath() . '.dat';
        $targetDir = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR);
        if (!$targetDir->isExist($dat) || strcmp($mTime, $targetDir->readFile($dat)) !== 0) {
            $this->strategy->merge($assetsToMerge, $resultAsset);
            $targetDir->writeFile($dat, $mTime);
        }
    }
}
