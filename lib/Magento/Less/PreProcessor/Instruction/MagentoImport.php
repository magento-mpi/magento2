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
     * @var array
     */
    protected $viewParams;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\View\Layout\File\SourceInterface $fileSource
     * @param \Magento\View\Service $viewService
     * @param \Magento\Logger $logger
     * @param array $viewParams
     */
    public function __construct(
        \Magento\View\Layout\File\SourceInterface $fileSource,
        \Magento\View\Service $viewService,
        \Magento\Logger $logger,
        array $viewParams = array()
    ) {
        $this->fileSource = $fileSource;
        $viewService->updateDesignParams($viewParams);
        $this->logger = $logger;
        $this->viewParams = $viewParams;
    }

    /**
     * {@inheritdoc}
     */
    public function process($lessContent)
    {
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
