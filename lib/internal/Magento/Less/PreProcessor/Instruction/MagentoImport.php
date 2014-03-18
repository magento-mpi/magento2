<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

use Magento\Less\PreProcessor\ErrorHandlerInterface;
use Magento\View\Asset\PreProcessorInterface;
use Magento\View\Asset\LocalInterface;
use Magento\View\Asset\FileId;
use Magento\View\Asset\PreProcessor\ModuleNotation;
use Magento\View\DesignInterface;
use Magento\View\File\SourceInterface;

/**
 * LESS @magento_import instruction preprocessor
 */
class MagentoImport implements PreProcessorInterface
{
    /**
     * PCRE pattern that matches @magento_import LESS instruction
     */
    const REPLACE_PATTERN = '#//@magento_import\s+[\'\"](?P<path>(?![/\\\]|\w:[/\\\])[^\"\']+)[\'\"]\s*?;#';

    /**
     * @var DesignInterface
     */
    protected $design;

    /**
     * @var SourceInterface
     */
    protected $fileSource;

    /**
     * @var ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * @param DesignInterface $design
     * @param SourceInterface $fileSource
     * @param ErrorHandlerInterface $errorHandler
     */
    public function __construct(
        DesignInterface $design,
        SourceInterface $fileSource,
        ErrorHandlerInterface $errorHandler
    ) {
        $this->design = $design;
        $this->fileSource = $fileSource;
        $this->errorHandler = $errorHandler;
    }

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
     * Replace @magento_import to @import less instructions
     *
     * @param array $matchedContent
     * @param FileId $asset
     * @return string
     */
    protected function replace(array $matchedContent, FileId $asset)
    {
        $importsContent = '';
        try {
            $matchedFileId = $matchedContent['path'];
            $resolvedPath = ModuleNotation::convertModuleNotationToPath($asset, $matchedFileId);
            $importFiles = $this->fileSource->getFiles($this->design->getDesignTheme(), $resolvedPath);
            /** @var $importFile \Magento\View\File */
            foreach ($importFiles as $importFile) {
                $importsContent .= $importFile->getModule()
                    ? "@import '{$importFile->getModule()}::{$importFile->getFilename()}';\n"
                    : "@import '{$importFile->getFilename()}';\n";
            }
        } catch (\LogicException $e) {
            $this->errorHandler->processException($e);
        }
        return $importsContent;
    }
}
