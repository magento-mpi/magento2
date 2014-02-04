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
     * @var \Magento\View\Layout\File\SourceInterface
     */
    protected $fileSource;

    /**
     * @var PreProcessor\ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * @var \Magento\View\RelatedFile
     */
    protected $relatedFile;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @param \Magento\View\Layout\File\SourceInterface $fileSource
     * @param \Magento\View\Service $viewService
     * @param \Magento\View\RelatedFile $relatedFile
     * @param PreProcessor\ErrorHandlerInterface $errorHandler
     */
    public function __construct(
        \Magento\View\Layout\File\SourceInterface $fileSource,
        \Magento\View\Service $viewService,
        \Magento\View\RelatedFile $relatedFile,
        PreProcessor\ErrorHandlerInterface $errorHandler
    ) {
        $this->fileSource = $fileSource;
        $this->viewService = $viewService;
        $this->relatedFile = $relatedFile;
        $this->errorHandler = $errorHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function process($lessContent, array $viewParams, array $paths = [])
    {
        $this->viewService->updateDesignParams($viewParams);
        $replaceCallback = function ($matchContent) use ($viewParams, $paths) {
            return $this->replace($matchContent, $viewParams, $paths);
        };
        return preg_replace_callback(self::REPLACE_PATTERN, $replaceCallback, $lessContent);
    }

    /**
     * Replace @magento_import to @import less instructions
     *
     * @param array $matchContent
     * @param array $viewParams
     * @param array $paths
     * @return string
     */
    protected function replace($matchContent, $viewParams, $paths)
    {
        $importsContent = '';
        try {
            $resolvedPath = $this->relatedFile->buildPath(
                $matchContent['path'],
                $paths['parentAbsolutePath'],
                $paths['parentPath'],
                $viewParams
            );
            $importFiles = $this->fileSource->getFiles($viewParams['themeModel'], $resolvedPath);
            /** @var $importFile \Magento\View\Layout\File */
            foreach ($importFiles as $importFile) {
                $importsContent .= "@import '{$importFile->getFilename()}';\n";
            }
        } catch(\LogicException $e) {
            $this->errorHandler->processException($e);
        }
        return $importsContent;
    }
}
