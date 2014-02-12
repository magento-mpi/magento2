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
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var \Magento\View\Publisher\FileFactory
     */
    protected $fileFactory;

    /**
     * @param \Magento\Less\PreProcessor $lessPreProcessor
     * @param AdapterInterface $adapter
     * @param \Magento\Logger $logger
     * @param \Magento\View\Publisher\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Less\PreProcessor $lessPreProcessor,
        AdapterInterface $adapter,
        \Magento\Logger $logger,
        \Magento\View\Publisher\FileFactory $fileFactory
    ) {
        $this->lessPreProcessor = $lessPreProcessor;
        $this->adapter = $adapter;
        $this->logger = $logger;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Process LESS file content
     *
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param \Magento\Filesystem\Directory\WriteInterface $targetDirectory
     * @return \Magento\View\Publisher\FileInterface
     */
    public function process(\Magento\View\Publisher\FileInterface $publisherFile, $targetDirectory)
    {
        // if css file has being already found_by_fallback or prepared_by_previous_pre-processor
        if ($publisherFile->getSourcePath()) {
            return $publisherFile;
        }

        try {
            $preparedLessFileSourcePath = $this->lessPreProcessor->processLessInstructions(
                $this->replaceExtension($publisherFile->getFilePath(), 'css', 'less'),
                $publisherFile->getViewParams()
            );
            $cssContent = $this->adapter->process($preparedLessFileSourcePath);
        } catch (\Magento\Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
            return $publisherFile;     // It has 'null' source path
        } catch (Adapter\AdapterException $e) {
            $this->logger->logException($e);
            return $publisherFile;     // It has 'null' source path
        }

        $tmpFilePath = Composite::TMP_VIEW_DIR . '/' . self::TMP_LESS_DIR . '/' . $publisherFile->getPublicationPath();
        $targetDirectory->writeFile($tmpFilePath, $cssContent);

        $processedFile = $this->fileFactory->create(
            $publisherFile->getFilePath(),
            $publisherFile->getViewParams(),
            $targetDirectory->getAbsolutePath($tmpFilePath)
        );

        return $processedFile;
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
