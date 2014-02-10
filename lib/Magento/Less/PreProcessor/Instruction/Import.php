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
     * @var \Magento\Less\PreProcessor
     */
    protected $preProcessor;

    /**
     * @var View\RelatedFile
     */
    protected $relatedFile;

    /**
     * @var PreProcessor\ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * @param PreProcessor $preProcessor
     * @param PreProcessor\ErrorHandlerInterface $errorHandler
     * @param View\RelatedFile $relatedFile
     */
    public function __construct(
        PreProcessor $preProcessor,
        PreProcessor\ErrorHandlerInterface $errorHandler,
        View\RelatedFile $relatedFile
    ) {
        $this->preProcessor = $preProcessor;
        $this->errorHandler = $errorHandler;
        $this->relatedFile = $relatedFile;
    }

    /**
     * Explode import paths
     *
     * @param array $matchedPaths
     * @param array $viewParams
     * @param array $params
     * @return array
     */
    protected function generatePaths($matchedPaths, $viewParams, array $params)
    {
        $importPaths = array();
        foreach ($matchedPaths as $path) {
            try {
                $tempViewParams = $viewParams;
                $resolvedPath = $this->relatedFile->buildPath(
                    $this->preparePath($path), $params['parentPath'], $tempViewParams
                );
                $importPaths[$path] = $this->preProcessor->processLessInstructions($resolvedPath, $tempViewParams);
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
    public function process($lessContent, array $viewParams, array $params = [])
    {
        $matches = [];
        preg_match_all(self::REPLACE_PATTERN, $lessContent, $matches);
        $importPaths = $this->generatePaths($matches['path'], $viewParams, $params);
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
        $path = $matchContent['path'];
        if (empty($importPaths[$path])) {
            return '';
        }
        $typeString = empty($matchContent['type']) ? '' : '(' . $matchContent['type'] . ') ';
        $mediaString = empty($matchContent['media']) ? '' : ' ' . $matchContent['media'];
        return "@import {$typeString}'{$importPaths[$path]}'{$mediaString};";
    }
}
