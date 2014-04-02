<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\View\Generator;

use Magento\View\Asset\File;

/**
 * A workaround for assets to suppress context of locale and file resolution
 *
 * The Generator tool doesn't use getSourceFile() feature, so dependency on "file source" is not necessary
 */
class Asset extends File
{
    public function __construct(File\Context $context, $fileId, $contentType)
    {
        $this->context = $context;
        list($this->module, $this->filePath) = self::extractModule($fileId);
        $this->contentType = $contentType;
    }
}
