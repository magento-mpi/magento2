<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

/**
 * Less @magento_import instruction preprocessor
 */
class MagentoImport extends AbstractImport
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
     * @param \Magento\View\Layout\File\SourceInterface $fileSource
     * @param \Magento\View\Service $viewService
     * @param \Magento\Less\PreProcessor $preProcessor
     * @param \Magento\Logger $logger
     * @param array $viewParams
     */
    public function __construct(
        \Magento\View\Layout\File\SourceInterface $fileSource,
        \Magento\View\Service $viewService,
        \Magento\Less\PreProcessor $preProcessor,
        \Magento\Logger $logger,
        array $viewParams = array()
    ) {
        $this->fileSource = $fileSource;
        $viewService->updateDesignParams($viewParams);
        parent::__construct($preProcessor, $logger, $viewParams);
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
     * Replace @magento_import to @import less instructions
     *
     * @param array $matchContent
     * @return string
     */
    protected function replace($matchContent)
    {
        $importsContent = '';
        try {
            $importFiles = $this->fileSource->getFiles($this->viewParams['themeModel'], $matchContent['path']);
            /** @var $importFile \Magento\View\Layout\File */
            foreach ($importFiles as $importFile) {
                $importsContent .= "@import '{$importFile->getFilename()}';\n";
            }
        } catch(\LogicException $e) {
            $this->logger->logException($e);
        }
        return $importsContent;
    }
}
