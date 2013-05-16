<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for different file merging strategies
 */
interface Mage_Core_Model_Page_Asset_MergeStrategy_MergeStrategyInterface
{
    /**
     * Merge files into one and save to disk
     *
     * @param array $publicFiles - list of full file paths to merge
     * @param string $destinationFile -  full file path for merged file
     * @return mixed
     */
    public function mergeFiles(array $publicFiles, $destinationFile);

    /**
     * Sets whether merge is being performed for css files
     *
     * @param bool $isCss
     * @return mixed
     */
    public function setIsCss($isCss);
}
