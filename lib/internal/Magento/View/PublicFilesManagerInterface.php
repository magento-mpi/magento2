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
    public function getPublicViewFile($filePath, array $params);

    /**
     * Get public file path without any publication
     *
     * @param string $filePath
     * @param array $params
     * @return string
     */
    public function getPublicViewFilePath($filePath, array $params);

    /**
     * Get path to requested file
     *
     * @param string $filePath
     * @param array $params
     * @return string
     */
    public function getViewFile($filePath, array $params);
}
