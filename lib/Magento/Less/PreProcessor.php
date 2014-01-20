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
 * LESS instruction pre-processor
 */
class PreProcessor
{
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
     * @return \Magento\Less\PreProcessorInterface[]
     */
    protected function getLessPreProcessors()
    {
        $preProcessors = [];
        foreach ($this->preProcessors as $preProcessorClass) {
            $preProcessors[] = $this->instructionFactory->create($preProcessorClass['class']);
        }
        return $preProcessors;
    }

    /**
     * @return \Magento\Filesystem\Directory\ReadInterface
     */
    protected function getDirectoryRead()
    {
        return $this->filesystem->getDirectoryRead(\Magento\Filesystem::ROOT);
    }

    /**
     * @return \Magento\Filesystem\Directory\WriteInterface
     */
    protected function getDirectoryWrite()
    {
        return $this->filesystem->getDirectoryWrite(\Magento\Filesystem::STATIC_VIEW);
    }

    /**
     * Return new source path for less file into temporary folder
     *
     * @param string $lessFileSourcePath
     * @return string
     */
    protected function generateNewPath($lessFileSourcePath)
    {
        $sourcePathPrefix = $this->getDirectoryRead()->getAbsolutePath();
        $targetPathPrefix = $this->getDirectoryWrite()->getAbsolutePath();
        return str_replace($sourcePathPrefix, $targetPathPrefix, $lessFileSourcePath);
    }

    /**
     * @param string $lessFilePath
     * @param array $params
     * @return string of saved or original pre-processed less file
     */
    public function processLessInstructions($lessFilePath, $params)
    {
        $lessFileSourcePath = $this->viewFileSystem->getViewFile($lessFilePath, $params);
        $directoryRead = $this->getDirectoryRead();
        $lessContent = $directoryRead->readFile($directoryRead->getRelativePath($lessFileSourcePath));
        foreach ($this->getLessPreProcessors() as $processor) {
            if ($processor instanceof \Magento\Less\PreProcessor\ImportInterface) {
                $importPaths = $processor->generatePaths($lessContent)->getImportPaths();
                foreach ($importPaths as $importPath) {
                    $lessFileTargetPath = $this->processLessInstructions($importPath, $params);
                    $processor->setImportPath($importPath, $lessFileTargetPath);
                }
            }
            $lessContent = $processor->process($lessContent);
        }
        $lessFileTargetPath = $this->generateNewPath($lessFileSourcePath);
        if (!empty($importPaths) && $lessFileSourcePath != $lessFileTargetPath) {
            $directoryWrite = $this->getDirectoryWrite();
            $directoryWrite->writeFile($directoryWrite->getRelativePath($lessFileTargetPath), $lessContent);
            return $lessFileTargetPath;
        }
        return $lessFileSourcePath;
    }
}
