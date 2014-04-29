<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset\MergeStrategy;

/**
 * Merge strategy representing the following: merged file is being recreated if and only if merged file does not exist
 */
class FileExists implements \Magento\Framework\View\Asset\MergeStrategyInterface
{
    /**
     * Strategy
     *
     * @var \Magento\Framework\View\Asset\MergeStrategyInterface
     */
    protected $strategy;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
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
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        $directory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::PUB_DIR);
        if (!$directory->isExist($directory->getRelativePath($destinationFile))) {
            $this->strategy->mergeFiles($publicFiles, $destinationFile, $contentType);
        }
    }
}
