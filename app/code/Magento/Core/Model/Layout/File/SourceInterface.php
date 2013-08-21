<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of locating layout files in the file system
 */
interface Magento_Core_Model_Layout_File_SourceInterface
{
    /**
     * Retrieve instances of layout files
     *
     * @param Magento_Core_Model_ThemeInterface $theme Theme that defines the design context
     * @return Magento_Core_Model_Layout_File[]
     */
    public function getFiles(Magento_Core_Model_ThemeInterface $theme);
}
