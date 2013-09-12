<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for search path resolution during fallback process
 */
interface Magento_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * Get ordered list of folders to search for a file
     *
     * @param array $params Values to substitute placeholders with
     * @return array folders to perform a search
     */
    public function getPatternDirs(array $params);
}
