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
     * Temporary directory prefix
     */
    const TMP_LESS_DIR   = 'less';

    /**
     * @var \Magento\Less\PreProcessor
     */
    protected $lessPreProcessor;

    /**
     * @var \Magento\Css\PreProcessor\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Less\PreProcessor $lessPreProcessor
     * @param AdapterInterface $adapter
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\Less\PreProcessor $lessPreProcessor,
        \Magento\Css\PreProcessor\AdapterInterface $adapter,
        \Magento\Logger $logger
    ) {
        $this->lessPreProcessor = $lessPreProcessor;
        $this->adapter = $adapter;
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
        // if css file has being already found_by_fallback or prepared_by_previous_pre-processor
        if ($publisherFile->getSourcePath()) {
            return $publisherFile->getSourcePath();
        }
        try {
            $preparedLessFileSourcePath = $this->lessPreProcessor->processLessInstructions(
                $this->replaceExtension($publisherFile->getFilePath(), 'css', 'less'),
                $publisherFile->getViewParams()
            );
            $cssContent = $this->adapter->process($preparedLessFileSourcePath);
        } catch (\Magento\Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
            return $publisherFile->getSourcePath();     // It's actually 'null'
        } catch (\Magento\Css\PreProcessor\Adapter\AdapterException $e) {
            $this->logger->logException($e);
            return $publisherFile->getSourcePath();     // It's actually 'null'
        }

        $tmpFilePath = \Magento\Css\PreProcessor\Composite::TMP_VIEW_DIR . '/' . self::TMP_LESS_DIR . '/'
            . $publisherFile->getPublicationPath();

        $targetDirectory->writeFile($tmpFilePath, $cssContent);
        return $targetDirectory->getAbsolutePath($tmpFilePath);
    }

    /**
     * @param string $filePath
     * @param string $search
     * @param string $replace
     * @return string
     */
    protected function replaceExtension($filePath, $search, $replace)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($extension === $search) {
            $dotPosition = strrpos($filePath, '.');
            $filePath = substr($filePath, 0, $dotPosition + 1) . $replace;
        }

        return $filePath;
    }
}
