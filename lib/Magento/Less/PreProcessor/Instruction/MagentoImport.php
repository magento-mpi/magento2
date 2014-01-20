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
    const REPLACE_PATTERN = '#//@magento_import\s+[\'\"](?P<path>[^\"\']+)[\'\"]\s*?;#';

    /**
     * @var \Magento\View\Layout\File\SourceInterface
     */
    protected $fileSource;

    /**
     * @var array
     */
    protected $viewParams;

    /**
     * @param \Magento\View\Layout\File\SourceInterface $fileSource
     * @param \Magento\View\Service $viewService
     * @param array $viewParams
     */
    public function __construct(
        \Magento\View\Layout\File\SourceInterface $fileSource,
        \Magento\View\Service $viewService,
        array $viewParams = array()
    ) {
        $this->fileSource = $fileSource;
        $viewService->updateDesignParams($viewParams);
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
        $importFiles = $this->fileSource->getFiles($this->viewParams['themeModel'], $matchContent['path']);
        /** @var $importFile \Magento\View\Layout\File */
        foreach ($importFiles as $importFile) {
            $importsContent .= "@import '{$importFile->getFilename()}';\n";
        }
        return $importsContent;
    }
}
