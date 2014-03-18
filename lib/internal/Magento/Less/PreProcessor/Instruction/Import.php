<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

use Magento\View\Asset\PreProcessorInterface;
use Magento\View\Asset\LocalInterface;
use Magento\View\Asset\FileId;
use Magento\View\Asset\PreProcessor\ModuleNotation;

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
     * @var array
     */
    protected $relatedFiles = array();

    /**
     * {@inheritdoc}
     */
    public function process($content, $contentType, LocalInterface $asset)
    {
        $replaceCallback = function ($matchContent) use ($asset) {
            return $this->replace($matchContent, $asset);
        };
        $content = preg_replace_callback(self::REPLACE_PATTERN, $replaceCallback, $content);
        return array($content, $contentType);
    }

    /**
     * Retrieve information on all related files, processed so far
     *
     * @return array
     */
    public function getRelatedFiles()
    {
        return $this->relatedFiles;
    }

    /**
     * Clear the record of related files, processed so far
     */
    public function resetRelatedFiles()
    {
        $this->relatedFiles = array();
    }

    /**
     * Add related file to the record of processed files
     *
     * @param string $matchedFileId
     * @param FileId $asset
     */
    protected function recordRelatedFile($matchedFileId, FileId $asset)
    {
        $this->relatedFiles[] = array($matchedFileId, $asset);
    }

    /**
     * Return replacement of an original @import directive
     *
     * @param array $matchedContent
     * @param FileId $asset
     * @return string
     */
    protected function replace(array $matchedContent, FileId $asset)
    {
        $matchedFileId = $this->fixFileExtension($matchedContent['path']);
        $this->recordRelatedFile($matchedFileId, $asset);
        $resolvedPath = ModuleNotation::convertModuleNotationToPath($asset, $matchedFileId);
        $typeString = empty($matchedContent['type']) ? '' : '(' . $matchedContent['type'] . ') ';
        $mediaString = empty($matchedContent['media']) ? '' : ' ' . $matchedContent['media'];
        return "@import {$typeString}'{$resolvedPath}'{$mediaString};";
    }

    /**
     * Resolve extension of imported asset according to the specification of LESS format
     *
     * @param string $fileId
     * @return string
     * @link http://lesscss.org/features/#import-directives-feature-file-extensions
     */
    protected function fixFileExtension($fileId)
    {
        if (!pathinfo($fileId, PATHINFO_EXTENSION)) {
            $fileId .= '.less';
        }
        return $fileId;
    }
}
