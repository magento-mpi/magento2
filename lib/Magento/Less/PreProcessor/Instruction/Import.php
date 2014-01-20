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
 * Import instruction object
 */
class Import implements PreProcessorInterface, \Magento\Less\PreProcessor\ImportInterface
{
    /**
     * Pattern of @import less instruction
     */
    const REPLACE_PATTERN = '#@import\s+(\((?P<type>\w+)\)\s+)?[\'\"](?P<path>[^\"\']+)[\'\"]\s*?(?P<media>.*?);#';

    /**
     * Import's path list where key is relative path and value is absolute path to the imported content
     *
     * @var array
     */
    protected $importPaths = [];

    /**
     * @return array of imported files of less
     */
    public function getImportPaths()
    {
        return array_keys($this->importPaths);
    }

    /**;
     * Explode import paths
     *
     * @param string $lessContent
     * @return $this
     */
    public function generatePaths($lessContent)
    {
        $matches = [];
        preg_match_all(self::REPLACE_PATTERN, $lessContent, $matches);
        foreach ($matches['path'] as $path) {
            $this->importPaths[$path] = null;
        }
        return $this;
    }

    /**
     * @param string $relativePath
     * @param string $absolutePath
     * @return $this
     */
    public function setImportPath($relativePath, $absolutePath)
    {
        if (array_key_exists($relativePath, $this->importPaths)) {
            $this->importPaths[$relativePath] = $absolutePath;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process($lessContent)
    {
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
        if (!isset($this->importPaths[$matchContent['path']])) {
            return '';
        }
        $typeString  = empty($matchContent['type']) ? '' : '(' . $matchContent['type'] . ') ';
        return "@import {$typeString}'{$this->importPaths[$matchContent['path']]}';";
    }
}
