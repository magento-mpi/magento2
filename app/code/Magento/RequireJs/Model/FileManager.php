<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Model;

/**
 * A service for handling RequireJS files in the application
 */
class FileManager
{
    /**
     * @var \Magento\RequireJs\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    public function __construct(
        \Magento\RequireJs\Config $config,
        \Magento\Framework\App\Filesystem $appFilesystem,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->config = $config;
        $this->filesystem = $appFilesystem;
        $this->appState = $appState;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Create a view asset representing the aggregated configuration file
     *
     * @return \Magento\Framework\View\Asset\File
     */
    public function createRequireJsAsset()
    {
        $relPath = $this->config->getConfigFileRelativePath();
        $this->ensureSourceFile($relPath);
        return $this->assetRepo->createArbitrary($relPath, '');
    }

    /**
     * Make sure the aggregated configuration is materialized
     *
     * By default write the file if it doesn't exist, but in developer mode always do it.
     *
     * @param string $relPath
     */
    private function ensureSourceFile($relPath)
    {
        $dir = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR);
        if ($this->appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER || !$dir->isExist($relPath)) {
            $dir->writeFile($relPath, $this->config->getConfig());
        }
    }
}
