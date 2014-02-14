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
     * @var InstructionFactory
     */
    protected $instructionFactory;

    /**
     * @var PreProcessor\FileListFactory
     */
    protected $fileListFactory;

    /**
     * @var PreProcessor\FileFactory
     */
    protected $fileFactory;

    /**
     * List of instruction pre-processors
     *
     * @var array
     */
    protected $preProcessors;

    /**
     * @param InstructionFactory $instructionFactory
     * @param PreProcessor\FileListFactory $fileListFactory
     * @param PreProcessor\FileFactory $fileFactory
     * @param array $preProcessors
     */
    public function __construct(
        InstructionFactory $instructionFactory,
        PreProcessor\FileListFactory $fileListFactory,
        PreProcessor\FileFactory $fileFactory,
        array $preProcessors = array()
    ) {
        $this->instructionFactory = $instructionFactory;
        $this->fileListFactory = $fileListFactory;
        $this->fileFactory = $fileFactory;
        $this->preProcessors = $preProcessors;
    }

    /**
     * Instantiate instruction less pre-processors
     *
     * @param PreProcessor\FileList $fileList
     * @return PreProcessorInterface[]
     */
    protected function initLessPreProcessors(PreProcessor\FileList $fileList)
    {
        $preProcessorsInstances = [];
        foreach ($this->preProcessors as $preProcessorClass) {
            $preProcessorsInstances[] = $this->instructionFactory->create($preProcessorClass['class'], [
                'fileList' => $fileList
            ]);
        }
        return $preProcessorsInstances;
    }

    /**
     * Process less file through preprocessors and all child files that was added during pre-processing
     *
     * @param string $lessFilePath
     * @param array $viewParams
     * @return string
     */
    public function processLessInstructions($lessFilePath, $viewParams)
    {
        /** @var $fileList PreProcessor\FileList */
        $fileList = $this->fileListFactory->create();
        $preProcessors = $this->initLessPreProcessors($fileList);
        $entryLessFile = $this->fileFactory->create(['filePath' => $lessFilePath, 'viewParams' => $viewParams]);
        $fileList->addFile($entryLessFile);
        /** @var $lessFile PreProcessor\File */
        foreach ($fileList as $lessFile) {
            $this->publishProcessedContent($preProcessors, $lessFile);
        }
        return $entryLessFile->getPublicationPath();
    }

    /**
     * Process less content and save
     *
     * @param PreProcessorInterface[] $preProcessors
     * @param PreProcessor\File $lessFile
     */
    public function publishProcessedContent(array $preProcessors, PreProcessor\File $lessFile)
    {
        $lessContent = $lessFile->getContent();
        foreach ($preProcessors as $processor) {
            $lessContent = $processor->process($lessFile, $lessContent);
        }
        $lessFile->saveContent($lessContent);
    }
}
