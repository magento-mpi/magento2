<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

/**
 * Css pre-processor adapter interface
 */
interface AdapterInterface
{
    /**
     * @param string $sourceFilePath
     * @return string
     */
    public function process($sourceFilePath);
}
