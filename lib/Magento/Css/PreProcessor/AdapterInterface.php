<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

/**
 * Adapter model interface
 */
interface AdapterInterface
{
    /**
     * @param $sourceFilePath
     * @return string
     */
    public function process($sourceFilePath);
}
