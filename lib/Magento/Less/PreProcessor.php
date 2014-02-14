<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less;

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
     * @var PreProcessor\File\LessFactory
     */
    protected $fileFactory;

    /**
     * List of instruction pre-processors
     *
     * @var array
     */
    protected $preProcessors;

    /**
     * @param PreProcessor\InstructionFactory $instructionFactory
     * @param PreProcessor\File\FileListFactory $fileListFactory
     * @param PreProcessor\File\LessFactory $fileFactory
     * @param array $preProcessors
     */
    public function __construct(
        PreProcessor\InstructionFactory $instructionFactory,
        PreProcessor\File\FileListFactory $fileListFactory,
        PreProcessor\File\LessFactory $fileFactory,
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
     * @param PreProcessor\File\FileList $fileList
     * @return PreProcessorInterface[]
     */
    protected function initLessPreProcessors(PreProcessor\File\FileList $fileList)
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
        /** @var $fileList PreProcessor\File\FileList */
        $fileList = $this->fileListFactory->create();
        $preProcessors = $this->initLessPreProcessors($fileList);
        $entryLessFile = $this->fileFactory->create(['filePath' => $lessFilePath, 'viewParams' => $viewParams]);
        $fileList->addFile($entryLessFile);
        /** @var $lessFile PreProcessor\File\Less */
        foreach ($fileList as $lessFile) {
            $this->publishProcessedContent($preProcessors, $lessFile);
        }
        return $entryLessFile->getPublicationPath();
    }

    /**
     * Process less content and save
     *
     * @param PreProcessorInterface[] $preProcessors
     * @param PreProcessor\File\Less $lessFile
     */
    public function publishProcessedContent(array $preProcessors, PreProcessor\File\Less $lessFile)
    {
        $lessContent = $lessFile->getContent();
        foreach ($preProcessors as $processor) {
            $lessContent = $processor->process($lessFile, $lessContent);
        }
        $lessFile->saveContent($lessContent);
    }
}
