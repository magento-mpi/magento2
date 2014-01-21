<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

/**
 * Less @import instruction preprocessor
 */
class Import extends AbstractImport
{
    /**
     * Pattern of @import less instruction
     */
    const REPLACE_PATTERN =
        '#@import\s+(\((?P<type>\w+)\)\s+)?[\'\"](?P<path>(?![/\\\]|\w:[/\\\])[^\"\']+)[\'\"]\s*?(?P<media>.*?);#';

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
