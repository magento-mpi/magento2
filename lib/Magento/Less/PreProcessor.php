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
     * @var array
     */
    protected $preProcessors;

    /**
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Filesystem $filesystem
     * @param InstructionFactory $instructionFactory
     * @param array $preProcessors
     */
    public function __construct(
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Filesystem $filesystem,
        InstructionFactory $instructionFactory,
        array $preProcessors = array()
    ) {
        $this->viewFileSystem = $viewFileSystem;
        $this->filesystem = $filesystem;
        $this->instructionFactory = $instructionFactory;
        $this->preProcessors = $preProcessors;
    }

    /**
     * Instantiate instruction less preprocessors
     *
     * @return \Magento\Less\PreProcessorInterface[]
     */
    protected function getLessPreProcessors()
    {
        $preProcessors = [];
        foreach ($this->preProcessors as $preProcessorClass) {
            $preProcessors[] = $this->instructionFactory->get($preProcessorClass['class']);
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
        return $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
    }

    /**
     * Get directory for publication temporary less files
     *
     * @return \Magento\Filesystem\Directory\WriteInterface
     */
    protected function getDirectoryWrite()
    {
        return $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::TMP_DIR);
    }

    /**
     * Generate new source path for less file into temporary folder
     *
     * @param string $lessFileSourcePath
     * @return string
     */
    protected function generatePath($lessFileSourcePath)
    {
        $sourcePathPrefix = $this->getDirectoryRead()->getAbsolutePath();
        $targetPathPrefix = $this->getDirectoryWrite()->getAbsolutePath() . self::PUBLICATION_PREFIX_PATH . '/';
        return str_replace($sourcePathPrefix, $targetPathPrefix, $lessFileSourcePath);
    }

    /**
     * Save pre-processed less content to temporary folder
     *
     * @param string $lessFileSourcePath absolute path to source less file
     * @param string $lessContent
     * @return string absolute path to the pre-processed less file
     */
    protected function saveLessFile($lessFileSourcePath, $lessContent)
    {
        $lessFileTargetPath = $this->generatePath($lessFileSourcePath);
        $directoryWrite = $this->getDirectoryWrite();
        $directoryWrite->writeFile($directoryWrite->getRelativePath($lessFileTargetPath), $lessContent);
        return $lessFileTargetPath;
    }

    /**
     * Process less content throughout all existed instruction preprocessors
     *
     * @param string $lessFilePath
     * @param array $viewParams
     * @return string of saved or original preprocessed less file
     */
    public function processLessInstructions($lessFilePath, $viewParams)
    {
        $lessFileTargetPath = $lessFileSourcePath = $this->viewFileSystem->getViewFile($lessFilePath, $viewParams);
        $directoryRead = $this->getDirectoryRead();
        $lessContent = $lessSourceContent = $directoryRead->readFile(
            $directoryRead->getRelativePath($lessFileSourcePath)
        );

        foreach ($this->getLessPreProcessors() as $processor) {
            $lessContent = $processor->process(
                $lessContent,
                $viewParams,
                ['parentPath' => $lessFilePath, 'parentAbsolutePath' => $lessFileSourcePath]
            );
        }

        if ($lessSourceContent != $lessContent) {
            $lessFileTargetPath = $this->saveLessFile($lessFileSourcePath, $lessContent);
        }
        return $lessFileTargetPath;
    }
}
