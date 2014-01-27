<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use \Magento\View\Asset\PreProcessor\PreProcessorInterface;

/**
 * Css pre-processor less
 */
class Less implements PreProcessorInterface
{
    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Less\PreProcessor
     */
    protected $lessPreProcessor;

    /**
     * @var \Magento\Css\PreProcessor\AdapterInterface
     */
    protected $adapter;

    /**
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Less\PreProcessor $lessPreProcessor
     * @param AdapterInterface $adapter
     */
    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Less\PreProcessor $lessPreProcessor,
        \Magento\Css\PreProcessor\AdapterInterface $adapter
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->lessPreProcessor = $lessPreProcessor;
        $this->adapter = $adapter;
    }

    /**
     * Process LESS file content
     *
     * @param string $filePath
     * @param array $params
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @param null|string $sourcePath
     * @return string
     */
    public function process($filePath, $params, $targetDirectory, $sourcePath = null)
    {
        // if css file has being already discovered/prepared by previous pre-processor
        if ($sourcePath) {
            return $sourcePath;
        }

        // TODO: if css file is already exist. May compare modification time of .less and .css files here.
        $sourcePath = $this->viewFileSystem->getViewFile($filePath, $params);

        $lessFilePath = str_replace('.css', '.less', $filePath);
        try {
            $preparedLessFileSourcePath = $this->lessPreProcessor->processLessInstructions($lessFilePath, $params);
        } catch (\Magento\Filesystem\FilesystemException $e) {
            return $sourcePath;
        }
        $cssContent = $this->adapter->process($preparedLessFileSourcePath);
        // doesn't matter where exact file has been found, we use original file identifier
        // see \Magento\View\Publisher::_buildPublishedFilePath() for details
        $targetDirectory->writeFile($filePath, $cssContent);
        return $targetDirectory->getAbsolutePath($filePath);
    }
}
