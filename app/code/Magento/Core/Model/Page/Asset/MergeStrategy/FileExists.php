<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Merge strategy representing the following: merged file is being recreated if and only if merged file does not exist
 */
namespace Magento\Core\Model\Page\Asset\MergeStrategy;

class FileExists
    implements \Magento\Core\Model\Page\Asset\MergeStrategyInterface
{
    /**
     * @var \Magento\Core\Model\Page\Asset\MergeStrategyInterface
     */
    private $_strategy;

    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @param \Magento\Core\Model\Page\Asset\MergeStrategyInterface $strategy
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Model\Page\Asset\MergeStrategyInterface $strategy,
        \Magento\Filesystem $filesystem
    ) {
        $this->_strategy = $strategy;
        $this->_filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        if (!$this->_filesystem->has($destinationFile)) {
            $this->_strategy->mergeFiles($publicFiles, $destinationFile, $contentType);
        }
    }
}
