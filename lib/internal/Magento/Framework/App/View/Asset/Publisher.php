<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\View\Asset;

use Magento\Framework\View\Asset;

/**
 * A publishing service for view assets
 */
class Publisher
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Filesystem $filesystem
    ) {
        $this->appState = $appState;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(Asset\LocalInterface $asset)
    {
        if ($this->appState->getMode() === \Magento\Framework\App\State::MODE_DEVELOPER) {
            return false;
        }
        $dir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR);
        if ($dir->isExist($asset->getPath())) {
            return true;
        }
        return $this->publishAsset($asset);
    }

    /**
     * Publish the asset
     *
     * @param Asset\LocalInterface $asset
     * @return bool
     */
    private function publishAsset(Asset\LocalInterface $asset)
    {
        $dir = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR);
        $rootDir = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $source = $rootDir->getRelativePath($asset->getSourceFile());
        $destination = $asset->getPath();
        return $rootDir->copyFile($source, $destination, $dir);
    }
}
