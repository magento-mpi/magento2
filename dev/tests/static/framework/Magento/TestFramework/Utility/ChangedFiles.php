<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Utility;

/**
 * A helper to gather various changed files
 * if INCREMENTAL_BUILD env variable is set by CI build infrastructure, only files changed in the
 * branch are gathered, otherwise all files
 */
class ChangedFiles
{
    /**
     * Returns array of PHP-files, that use or declare Magento application classes and Magento libs
     *
     * @param string $changedFilesList
     * @return array
     */
    public static function getPhpFiles($changedFilesList)
    {
        $fileHelper = \Magento\TestFramework\Utility\Files::init();
        $allPhpFiles = $fileHelper->getPhpFiles();
        if (isset($_ENV['INCREMENTAL_BUILD'])) {
            $phpFiles = file($changedFilesList, FILE_IGNORE_NEW_LINES);
            foreach ($phpFiles as $key => $phpFile) {
                $phpFiles[$key] = $fileHelper->getPathToSource() . '/' . $phpFile;
            }
            $phpFiles = \Magento\TestFramework\Utility\Files::composeDataSets($phpFiles);
            $phpFiles = array_intersect_key($phpFiles, $allPhpFiles);
        } else {
            $phpFiles = $allPhpFiles;
        }

        return $phpFiles;
    }
}
