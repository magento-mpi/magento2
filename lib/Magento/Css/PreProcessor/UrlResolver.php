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
 * Css pre-processor url resolver
 */
class UrlResolver implements PreProcessorInterface
{
    /**
     */
    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Logger $logger
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->logger = $logger;
    }

    /**
     * Process LESS file content
     *
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return string
     */
    public function process(\Magento\View\Publisher\FileInterface $publisherFile, $targetDirectory)
    {
        $filePath = $this->_viewFileSystem->normalizePath($publisherFile->getFilePath());
        $sourcePath = $this->_viewFileSystem->normalizePath($publisherFile->getSourcePath());
        $targetPath = $this->pathBuilder->buildPublishedFilePath($publisherFile);
        $cssContent = $this->_getPublicCssContent(
            $sourcePath,
            $targetPath,
            $filePath,
            $publisherFile->getViewParams()
        );
        /**
         * TODO: Decide where it should be
         */
//        $targetDirectory = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
//        $targetDirectory->writeFile($targetPathRelative, $cssContent);
        return $publisherFile->getSourcePath();
    }

    /**
     * Retrieve processed CSS file content that contains URLs relative to the specified public directory
     *
     * @param string $sourcePath Absolute path to the current location of CSS file
     * @param string $publicPath Absolute path to location of the CSS file, where it will be published
     * @param string $fileName File name used for reference
     * @param array $params Design parameters
     * @return string
     */
    protected function _getPublicCssContent($sourcePath, $publicPath, $fileName, $params)
    {
        $content = $this->rootDirectory->readFile($this->rootDirectory->getRelativePath($sourcePath));

        $callback = function ($fileId, $originalPath) use ($fileName, $params) {
            $relatedPathPublic = $this->_publishRelatedViewFile(
                $fileId,
                $originalPath,
                $fileName,
                $params
            );
            return $relatedPathPublic;
        };
        try {
            $content = $this->_cssUrlResolver->replaceCssRelativeUrls($content, $sourcePath, $publicPath, $callback);
        } catch (\Magento\Exception $e) {
            $this->_logger->logException($e);
        }
        return $content;
    }
}
