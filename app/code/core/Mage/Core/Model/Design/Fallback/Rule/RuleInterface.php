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
 * Interface for search path resolution during fallback process
 */
interface Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * Get ordered list of folders to search for a file
     *
     * @param string $fileName - relative file name
     * @param array $params - array of parameters
     * @param array $themeList - ordered array of themes - current theme and all its parents
     * @return array of folders to perform a search
     */
    public function getPatternDirs($fileName, $params, $themeList);
}
