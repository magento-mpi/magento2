<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

/**
 * Merge strategy representing the following: merged file is being recreated if and only if merged file does not exist
 */
class FileExists implements \Magento\View\Asset\MergeStrategyInterface
{
    /**
     * @var \Magento\View\Asset\MergeStrategyInterface
     */
    protected $strategy;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\View\Asset\MergeStrategyInterface $strategy
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\View\Asset\MergeStrategyInterface $strategy,
        \Magento\Filesystem $filesystem
    ) {
        $this->strategy = $strategy;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        if (!$this->filesystem->getDirectoryRead(\Magento\Filesystem::ROOT)->isExist($destinationFile)) {
            $this->strategy->mergeFiles($publicFiles, $destinationFile, $contentType);
        }
    }
}
