<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\MergeStrategy;

/**
 * Merge strategy representing the following: merged file is being recreated if and only if file does not exist
 * or meta-file does not exist or checksums do not match
 */
class Checksum implements \Magento\View\Asset\MergeStrategyInterface
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
        $directory = $this->filesystem->getDirectoryWrite(\Magento\Filesystem::PUB);
        $mergedMTimeFile = $directory->getRelativePath($destinationFile . '.dat');

        // Check whether we have already merged these files
        $filesMTimeData = '';
        foreach ($publicFiles as $file) {
            $filesMTimeData .= $directory->stat($directory->getRelativePath($file))['mtime'];
        }
        if (!($directory->isExist($destinationFile) && $directory->isExist($mergedMTimeFile)
            && (strcmp($filesMTimeData, $directory->readFile($mergedMTimeFile)) == 0))
        ) {
            $this->strategy->mergeFiles($publicFiles, $destinationFile, $contentType);
            $directory->writeFile($mergedMTimeFile, $filesMTimeData);
        }
    }
}
