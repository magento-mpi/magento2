<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

use Magento\Less\PreProcessor;
use Magento\Less\PreProcessorInterface;
use Magento\View;

/**
 * Less @magento_import instruction preprocessor
 */
class MagentoImport implements PreProcessorInterface
{
    /**
     * Pattern of @import less instruction
     */
    const REPLACE_PATTERN = '#//@magento_import\s+[\'\"](?P<path>(?![/\\\]|\w:[/\\\])[^\"\']+)[\'\"]\s*?;#';

    /**
     * Layout file source
     *
     * @var \Magento\View\File\SourceInterface
     */
    protected $fileSource;

    /**
     * Pre-processor error handler
     *
     * @var PreProcessor\ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * Related file
     *
     * @var \Magento\View\RelatedFile
     */
    protected $relatedFile;

    /**
     * View service
     *
     * @var \Magento\View\Asset\Service
     */
    protected $assetService;

    /**
     * @param View\File\SourceInterface $fileSource
     * @param View\Asset\Service $assetService
     * @param View\RelatedFile $relatedFile
     * @param PreProcessor\ErrorHandlerInterface $errorHandler
     */
    public function __construct(
        View\File\SourceInterface $fileSource,
        View\Asset\Service $assetService,
        View\RelatedFile $relatedFile,
        PreProcessor\ErrorHandlerInterface $errorHandler
    ) {
        $this->fileSource = $fileSource;
        $this->assetService = $assetService;
        $this->relatedFile = $relatedFile;
        $this->errorHandler = $errorHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PreProcessor\File\Less $lessFile, $lessContent)
    {
        $viewParams = $lessFile->getViewParams();
        $parentPath = $lessFile->getFilePath();
        $this->assetService->updateDesignParams($viewParams);
        $replaceCallback = function ($matchContent) use ($viewParams, $parentPath) {
            return $this->replace($matchContent, $viewParams, $parentPath);
        };
        return preg_replace_callback(self::REPLACE_PATTERN, $replaceCallback, $lessContent);
    }

    /**
     * Replace @magento_import to @import less instructions
     *
     * @param array $matchContent
     * @param array $viewParams
     * @param string $parentPath
     * @return string
     */
    protected function replace($matchContent, $viewParams, $parentPath)
    {
        $importsContent = '';
        try {
            $resolvedPath = $this->relatedFile->buildPath($matchContent['path'], $parentPath, $viewParams);

            $filePath = pathinfo($resolvedPath, PATHINFO_EXTENSION)
                ? $resolvedPath
                : rtrim($resolvedPath, '.') . '.less';

            $importFiles = $this->fileSource->getFiles($viewParams['themeModel'], $filePath);
            /** @var $importFile \Magento\View\File */
            foreach ($importFiles as $importFile) {
                $importsContent .=  $importFile->getModule()
                    ? "@import '{$importFile->getModule()}::{$resolvedPath}';\n"
                    : "@import '{$matchContent['path']}';\n";
            }
        } catch (\LogicException $e) {
            $this->errorHandler->processException($e);
        }
        return $importsContent;
    }
}
