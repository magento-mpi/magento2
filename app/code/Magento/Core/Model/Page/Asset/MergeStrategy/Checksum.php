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
     * @var \Magento\Filesystem\Directory\Write
     */
    private $_directory;

    /**
     * @param \Magento\Core\Model\Page\Asset\MergeStrategyInterface $strategy
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Model\Page\Asset\MergeStrategyInterface $strategy,
        \Magento\Filesystem $filesystem
    ) {
        $this->_strategy = $strategy;
        $this->_directory = $filesystem->getDirectoryWrite(\Magento\Filesystem\DirectoryList::PUB);
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
            $filesMTimeData .= $this->_directory->stat($this->_directory->getRelativePath($file))['mtime'];
        }
        if (!($this->_directory->isExist($this->_directory->getRelativePath($destinationFile)))
            && $this->_directory->isExist($this->_directory->getRelativePath($mergedMTimeFile))
            && (strcmp($filesMTimeData, $this->_directory->readFile(
                    $this->_directory->getRelativePath($mergedMTimeFile))) == 0)
        ) {
        $this->_strategy->mergeFiles($publicFiles, $destinationFile, $contentType);
        $this->_directory->writeFile($this->_directory->getRelativePath($mergedMTimeFile), $filesMTimeData);
        }
    }
}
