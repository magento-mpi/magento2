<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Merge strategy representing the following: merged file is being recreated if and only if file does not exist
 * or meta-file does not exist or checksums do not match
 */
namespace Magento\Core\Model\Page\Asset\MergeStrategy;

class Checksum
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
        $mergedMTimeFile = $destinationFile . '.dat';

        // Check whether we have already merged these files
        $filesMTimeData = '';
        foreach ($publicFiles as $file) {
            $filesMTimeData .= $this->_filesystem->getMTime($file);
        }
        if (!($this->_filesystem->has($destinationFile) && $this->_filesystem->has($mergedMTimeFile)
            && (strcmp($filesMTimeData, $this->_filesystem->read($mergedMTimeFile)) == 0))
        ) {
            $this->_strategy->mergeFiles($publicFiles, $destinationFile, $contentType);
            $this->_filesystem->write($mergedMTimeFile, $filesMTimeData);
        }
    }
}
