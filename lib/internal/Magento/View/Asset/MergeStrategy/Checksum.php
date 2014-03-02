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
        $file = $resultAsset->getSourceFile();
        $dat = $file . '.dat';

        $dir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $dat = $dir->getRelativePath($dat);
        $mTime = '';
        /** @var \Magento\View\Asset\MergeableInterface $asset */
        foreach ($assetsToMerge as $asset) {
            $assetFile = $dir->getRelativePath($asset->getSourceFile());
            $mTime .= $dir->stat($assetFile)['mtime'];
        }
        if (!$dir->isExist($dat) || strcmp($mTime, $dir->readFile($dat)) !== 0) {
            $this->strategy->merge($assetsToMerge, $resultAsset);
            $dir->writeFile($dat, $mTime);
        }
    }
}
