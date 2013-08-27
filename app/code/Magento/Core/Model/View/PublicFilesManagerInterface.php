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
interface Magento_Core_Model_View_PublicFilesManagerInterface
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
