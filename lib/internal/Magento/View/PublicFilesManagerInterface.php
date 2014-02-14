<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * FileSystem Interface
 */
interface PublicFilesManagerInterface
{
    /**
     * Get public file path
     *
     * @param string $filePath
     * @param array $params
     * @return string
     */
    public function getPublicFilePath($filePath, $params);
}
