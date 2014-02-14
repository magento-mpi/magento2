<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

use Magento\Less\PreProcessorInterface;
use Magento\Less\PreProcessor;
use Magento\View;

/**
 * Less @import instruction preprocessor
 */
class Import implements PreProcessorInterface
{
    /**
     * Pattern of @import less instruction
     */
    const REPLACE_PATTERN =
        '#@import\s+(\((?P<type>\w+)\)\s+)?[\'\"](?P<path>(?![/\\\]|\w:[/\\\])[^\"\']+)[\'\"]\s*?(?P<media>.*?);#';

    /**
     * @var PreProcessor\File\FileList
     */
    protected $fileList;

    /**
     * @var PreProcessor\File\LessFactory
     */
    protected $fileFactory;

    /**
     * @var PreProcessor\ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * @var View\RelatedFile
     */
    protected $relatedFile;

    /**
     * @param View\RelatedFile $relatedFile
     * @param PreProcessor\ErrorHandlerInterface $errorHandler
     * @param PreProcessor\File\FileList $fileList
     * @param PreProcessor\File\LessFactory $fileFactory
     */
    public function __construct(
        View\RelatedFile $relatedFile,
        PreProcessor\ErrorHandlerInterface $errorHandler,
        PreProcessor\File\FileList $fileList,
        PreProcessor\File\LessFactory $fileFactory
    ) {
        $this->relatedFile = $relatedFile;
        $this->errorHandler = $errorHandler;
        $this->fileList = $fileList;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Explode import paths
     *
     * @param \Magento\Less\PreProcessor\File\Less $lessFile
     * @param array $matchedPaths
     * @return array
     */
    protected function generatePaths(PreProcessor\File\Less $lessFile, $matchedPaths)
    {
        $importPaths = array();
        foreach ($matchedPaths as $path) {
            try {
                $viewParams = $lessFile->getViewParams();
                $resolvedPath = $this->relatedFile->buildPath(
                    $this->preparePath($path),
                    $lessFile->getFilePath(),
                    $viewParams
                );
                $importedLessFile = $this->fileFactory->create([
                    'filePath'   => $resolvedPath,
                    'parentFile' => $lessFile,
                    'viewParams' => $viewParams
                ]);
                $this->fileList->addFile($importedLessFile);
                $importPaths[$path] = $importedLessFile->getPublicationPath();
            } catch (\Magento\Filesystem\FilesystemException $e) {
                $this->errorHandler->processException($e);
            }
        }
        return $importPaths;
    }

    /**
     * Prepare relative path to less compatible state
     *
     * @param string $lessSourcePath
     * @return string
     */
    protected function preparePath($lessSourcePath)
    {
        return pathinfo($lessSourcePath, PATHINFO_EXTENSION) ? $lessSourcePath : $lessSourcePath . '.less';
    }

    /**
     * {@inheritdoc}
     */
    public function process(PreProcessor\File\Less $lessFile, $lessContent)
    {
        $matches = [];
        preg_match_all(self::REPLACE_PATTERN, $lessContent, $matches);
        $importPaths = $this->generatePaths($lessFile, $matches['path']);
        $replaceCallback = function ($matchContent) use ($importPaths) {
            return $this->replace($matchContent, $importPaths);
        };
        return preg_replace_callback(self::REPLACE_PATTERN, $replaceCallback, $lessContent);
    }

    /**
     * Replace import path to file
     *
     * @param array $matchContent
     * @param array $importPaths
     * @return string
     */
    protected function replace($matchContent, $importPaths)
    {
        if (empty($importPaths[$matchContent['path']])) {
            return '';
        }
        $filePath = $importPaths[$matchContent['path']];
        $typeString = empty($matchContent['type']) ? '' : '(' . $matchContent['type'] . ') ';
        $mediaString = empty($matchContent['media']) ? '' : ' ' . $matchContent['media'];
        return "@import {$typeString}'{$filePath}'{$mediaString};";
    }
}
