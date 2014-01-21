<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less;

use Magento\Less\PreProcessor\InstructionFactory;

/**
 * LESS instruction preprocessor
 */
class PreProcessor
{
    /**
     * Folder for publication preprocessed less files
     */
    const PUBLICATION_PREFIX_PATH = 'less';

    /**
     * @var \Magento\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var InstructionFactory
     */
    protected $instructionFactory;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $preProcessors;

    /**
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Filesystem $filesystem
     * @param InstructionFactory $instructionFactory
     * @param \Magento\Logger $logger
     * @param array $preProcessors
     */
    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Filesystem $filesystem,
        InstructionFactory $instructionFactory,
        \Magento\Logger $logger,
        array $preProcessors = array()
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->filesystem = $filesystem;
        $this->instructionFactory = $instructionFactory;
        $this->logger = $logger;
        $this->preProcessors = $preProcessors;
    }

    /**
     * Instantiate instruction less preprocessors
     *
     * @param array $params
     * @return \Magento\Less\PreProcessorInterface[]
     */
    protected function getLessPreProcessors(array $params)
    {
        $preProcessors = [];
        foreach ($this->preProcessors as $preProcessorClass) {
            $preProcessors[] = $this->instructionFactory->create($preProcessorClass['class'], $params);
        }
        return $preProcessors;
    }

    /**
     * Get base directory with source of less files
     *
     * @return \Magento\Filesystem\Directory\ReadInterface
     */
    protected function getDirectoryRead()
    {
        return $this->filesystem->getDirectoryRead(\Magento\Filesystem::ROOT);
    }

    /**
     * Get directory for publication temporary less files
     *
     * @return \Magento\Filesystem\Directory\WriteInterface
     */
    protected function getDirectoryWrite()
    {
        return $this->filesystem->getDirectoryWrite(\Magento\Filesystem::TMP);
    }

    /**
     * Generate new source path for less file into temporary folder
     *
     * @param string $lessFileSourcePath
     * @return string
     */
    protected function generateNewPath($lessFileSourcePath)
    {
        $sourcePathPrefix = $this->getDirectoryRead()->getAbsolutePath();
        $targetPathPrefix = $this->getDirectoryWrite()->getAbsolutePath() . self::PUBLICATION_PREFIX_PATH . '/';
        return str_replace($sourcePathPrefix, $targetPathPrefix, $lessFileSourcePath);
    }

    /**
     * Process less content throughout all existed instruction preprocessors
     *
     * @param string $lessFilePath
     * @param array $params
     * @return string of saved or original preprocessed less file
     */
    public function processLessInstructions($lessFilePath, $params)
    {
        $lessFileSourcePath = $this->viewFileSystem->getViewFile($lessFilePath, $params);
        $directoryRead = $this->getDirectoryRead();
        $lessContent = $lessSourceContent = $directoryRead->readFile(
            $directoryRead->getRelativePath($lessFileSourcePath)
        );

        foreach ($this->getLessPreProcessors($params) as $processor) {
            $lessContent = $processor->process($lessContent);
        }

        $lessFileTargetPath = $this->generateNewPath($lessFileSourcePath);
        if ($lessFileSourcePath != $lessFileTargetPath && $lessSourceContent != $lessContent) {
            $directoryWrite = $this->getDirectoryWrite();
            $directoryWrite->writeFile($directoryWrite->getRelativePath($lessFileTargetPath), $lessContent);
            return $lessFileTargetPath;
        }

        return $lessFileSourcePath;
    }
}
