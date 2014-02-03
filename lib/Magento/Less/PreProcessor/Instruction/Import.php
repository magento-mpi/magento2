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
     * Import's path list where key is relative path and value is absolute path to the imported content
     *
     * @var array
     */
    protected $importPaths = [];

    /**
     * @var \Magento\Less\PreProcessor
     */
    protected $preProcessor;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $viewParams;

    /**
     * @param \Magento\Less\PreProcessor $preProcessor
     * @param \Magento\Logger $logger
     * @param array $viewParams
     */
    public function __construct(
        \Magento\Less\PreProcessor $preProcessor,
        \Magento\Logger $logger,
        array $viewParams = array()
    ) {
        $this->preProcessor = $preProcessor;
        $this->logger = $logger;
        $this->viewParams = $viewParams;
    }

    /**
     * Explode import paths
     *
     * @param array $importPaths
     * @return $this
     */
    protected function generatePaths($importPaths)
    {
        foreach ($importPaths as $path) {
            $path = $this->preparePath($path);
            try {
                $this->importPaths[$path] = $this->preProcessor->processLessInstructions($path, $this->viewParams);
            } catch (\Magento\Filesystem\FilesystemException $e) {
                $this->logger->logException($e);
            }
        }
        return $this;
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
    public function process($lessContent)
    {
        $matches = [];
        preg_match_all(self::REPLACE_PATTERN, $lessContent, $matches);
        $this->generatePaths($matches['path']);
        return preg_replace_callback(self::REPLACE_PATTERN, array($this, 'replace'), $lessContent);
    }

    /**
     * Replace import path to file
     *
     * @param array $matchContent
     * @return string
     */
    protected function replace($matchContent)
    {
        $path = $this->preparePath($matchContent['path']);
        if (empty($this->importPaths[$path])) {
            return '';
        }
        $typeString  = empty($matchContent['type']) ? '' : '(' . $matchContent['type'] . ') ';
        return "@import {$typeString}'{$this->importPaths[$path]}';";
    }
}
