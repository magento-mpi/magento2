<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Less\PreProcessor\Instruction;

use Magento\Less\PreProcessor\ErrorHandlerInterface;
use Magento\Framework\View\Asset\PreProcessorInterface;
use Magento\Framework\View\Asset\LocalInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\File\CollectorInterface;

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
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @param DesignInterface $design
     * @param CollectorInterface $fileSource
     * @param ErrorHandlerInterface $errorHandler
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        DesignInterface $design,
        CollectorInterface $fileSource,
        ErrorHandlerInterface $errorHandler,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->design = $design;
        $this->fileSource = $fileSource;
        $this->errorHandler = $errorHandler;
        $this->assetRepo = $assetRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function process(\Magento\Framework\View\Asset\PreProcessor\Chain $chain)
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
            $relatedAsset = $this->assetRepo->createRelated($matchedFileId, $asset);
            $resolvedPath = $relatedAsset->getFilePath();
            $importFiles = $this->fileSource->getFiles($this->design->getDesignTheme(), $resolvedPath);
            /** @var $importFile \Magento\Framework\View\File */
            foreach ($importFiles as $importFile) {
                $importsContent .= $importFile->getModule()
                    ? "@import '{$importFile->getModule()}::{$resolvedPath}';\n"
                    : "@import '{$matchedFileId}';\n";
            }
        } catch (\LogicException $e) {
            $this->errorHandler->processException($e);
        }
        return $importsContent;
    }
}
