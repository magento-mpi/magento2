<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Asset\Plugin;

class CleanMergedJsCss
{
    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $database;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Core\Helper\File\Storage\Database $database
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Helper\File\Storage\Database $database,
        \Magento\App\Filesystem $filesystem
    ) {
        $this->database = $database;
        $this->filesystem = $filesystem;
    }

    /**
     * Clean files in database on cleaning merged assets
     *
     * @param \Magento\View\Asset\MergeService $subject
     * @param callable $proceed
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCleanMergedJsCss(
        \Magento\View\Asset\MergeService $subject,
        \Closure $proceed
    ) {
        $proceed();

        /** @var \Magento\Filesystem\Directory\ReadInterface $pubCacheDirectory */
        $pubCacheDirectory = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::PUB_VIEW_CACHE_DIR);
        $mergedDir = $pubCacheDirectory->getAbsolutePath() . '/' . \Magento\View\Asset\Merged::PUBLIC_MERGE_DIR;
        $this->database->deleteFolder($mergedDir);
    }
}
