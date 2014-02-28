<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * View files manager interface
 */
interface FilesManagerInterface
{
    /**
     * Get public file path
     *
     * @param string $filePath
     * @param array $params Required items: 'area', 'themeModel', 'locale'. Optional items: 'theme', 'themeId', 'module'
     * @return string
     */
    public function getPublicViewFile($filePath, array $params);

    /**
     * Get original file
     *
     * @param string $filePath
     * @param array $params Required items: 'area', 'themeModel', 'locale'. Optional items: 'theme', 'themeId', 'module'
     * @return string
     */
    public function getViewFile($filePath, array $params);
}
