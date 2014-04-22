<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\View\Generator;

use Magento\Framework\View\Asset\File;

/**
 * A workaround for assets to suppress context of locale and file resolution
 *
 * The Generator tool doesn't use getSourceFile() feature, so dependency on "file source" is not necessary
 */
class Asset extends File
{
    /**
     * @param File\Context $context
     * @param \Magento\Framework\View\Asset\ContextInterface $filePath
     * @param string $module
     * @param string $contentType
     */
    public function __construct(File\Context $context, $filePath, $module, $contentType)
    {
        $this->context = $context;
        $this->filePath = $filePath;
        $this->module = $module;
        $this->contentType = $contentType;
    }
}
