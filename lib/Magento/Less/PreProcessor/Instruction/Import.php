<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

use Magento\Less\PreProcessorInterface;

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
     * @var \Magento\View\RelatedFile
     */
    protected $relatedFile;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Less\PreProcessor $preProcessor
     * @param \Magento\View\RelatedFile $relatedFile
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\Less\PreProcessor $preProcessor,
        \Magento\View\RelatedFile $relatedFile,
        \Magento\Logger $logger
    ) {
        $this->preProcessor = $preProcessor;
        $this->relatedFile = $relatedFile;
        $this->logger = $logger;
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
            $resolvedPath = $this->relatedFile->buildPath(
                $this->preparePath($path),
                $params['parentAbsolutePath'],
                $params['parentPath'],
                $viewParams
            );
            try {
                $importPaths[$path] = $this->preProcessor->processLessInstructions(
                    $resolvedPath,
                    $viewParams
                );
            } catch (\Magento\Filesystem\FilesystemException $e) {
                $this->logger->logException($e);
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
     * @param $importPaths
     * @return string
     */
    protected function replace($matchContent, $importPaths)
    {
        $path = $matchContent['path'];
        if (empty($importPaths[$path])) {
            return '';
        }
        $typeString  = empty($matchContent['type']) ? '' : '(' . $matchContent['type'] . ') ';
        return "@import {$typeString}'{$importPaths[$path]}';";
    }
}
