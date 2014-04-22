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
use Magento\View\DesignInterface;
use Magento\View\File\CollectorInterface;

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
     * @var CollectorInterface
     */
    protected $fileSource;

    /**
     * @var ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * @var \Magento\View\Asset\ModuleNotation\Resolver
     */
    protected $notationResolver;

    /**
     * @param DesignInterface $design
     * @param CollectorInterface $fileSource
     * @param ErrorHandlerInterface $errorHandler
     * @param \Magento\View\Asset\ModuleNotation\Resolver $notationResolver
     */
    public function __construct(
        DesignInterface $design,
        CollectorInterface $fileSource,
        ErrorHandlerInterface $errorHandler,
        \Magento\View\Asset\ModuleNotation\Resolver $notationResolver
    ) {
        $this->design = $design;
        $this->fileSource = $fileSource;
        $this->errorHandler = $errorHandler;
        $this->notationResolver = $notationResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process(\Magento\View\Asset\PreProcessor\Chain $chain)
    {
        $asset = $chain->getAsset();
        $replaceCallback = function ($matchContent) use ($asset) {
            return $this->replace($matchContent, $asset);
        };
        $chain->setContent(preg_replace_callback(self::REPLACE_PATTERN, $replaceCallback, $chain->getContent()));
    }

    /**
     * Replace @magento_import to @import less instructions
     *
     * @param array $matchedContent
     * @param LocalInterface $asset
     * @return string
     */
    protected function replace(array $matchedContent, LocalInterface $asset)
    {
        $importsContent = '';
        try {
            $matchedFileId = $matchedContent['path'];
            $resolvedPath = $this->notationResolver->convertModuleNotationToPath($asset, $matchedFileId);
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
