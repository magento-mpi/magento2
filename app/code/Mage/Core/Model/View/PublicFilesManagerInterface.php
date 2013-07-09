<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * FileSystem Interface
 */
interface Mage_Core_Model_View_PublicFilesManagerInterface
{
    /**
     * Get public file path
     *
     * @param $filePath
     * @param array $params
     * @internal param string $file
     * @return string
     */
    public function getPublicFilePath($filePath, $params);

}
