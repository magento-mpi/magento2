<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Less\PreProcessor\Instruction;

use Magento\Framework\Less\PreProcessor\ErrorHandlerInterface;
use Magento\Framework\View\Asset\PreProcessorInterface;
use Magento\Framework\View\Asset\LocalInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\Asset\File\FallbackContext;
use Magento\Framework\View\Design\Theme\FlyweightFactory;

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
     * @var FlyweightFactory
     */
    protected $themeFactory;

    /**
     * @param DesignInterface $design
     * @param CollectorInterface $fileSource
     * @param ErrorHandlerInterface $errorHandler
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param FlyweightFactory $themeFactory
     */
    public function __construct(
        DesignInterface $design,
        CollectorInterface $fileSource,
        ErrorHandlerInterface $errorHandler,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        FlyweightFactory $themeFactory
    ) {
        $this->design = $design;
        $this->fileSource = $fileSource;
        $this->errorHandler = $errorHandler;
        $this->assetRepo = $assetRepo;
        $this->themeFactory = $themeFactory;
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
            $importFiles = $this->fileSource->getFiles($this->getTheme($relatedAsset), $resolvedPath);
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

    /**
     * Get theme model based on the information from asset
     *
     * @param LocalInterface $asset
     * @return \Magento\Framework\View\Design\ThemeInterface
     */
    protected function getTheme(LocalInterface $asset)
    {
        $context = $asset->getContext();
        if ($context instanceof FallbackContext) {
            return $this->themeFactory->create($context->getThemePath(), $context->getAreaCode());
        }
        return $this->design->getDesignTheme();
    }
}
