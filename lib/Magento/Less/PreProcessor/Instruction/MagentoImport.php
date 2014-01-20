<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

use Magento\Less\FileResolver;
use Magento\Less\PreProcessorInterface;

/**
 * Import instruction object
 */
class MagentoImport implements PreProcessorInterface
{
    /**
     * Pattern of @import less instruction
     */
    const REPLACE_PATTERN = '#//@magento_import\s+(?P<path>[\'\"][^\"\']+[\'\"])\s*?;#';

    /**
     * @var \Magento\Less\FileResolver
     */
    protected $fileResolver;

    /**
     * @param FileResolver $fileResolver
     */
    public function __construct(FileResolver $fileResolver)
    {
        $this->fileResolver = $fileResolver;
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
        $importFiles = $this->fileResolver->get($matchContent['path']);
        foreach ($importFiles as $importFile) {
            $importsContent .= "@import '{$importFile}';\n";
        }
        return $importsContent;
    }
}
