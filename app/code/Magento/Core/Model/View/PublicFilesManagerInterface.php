<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * FileSystem Interface
 */
namespace Magento\Core\Model\View;

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
