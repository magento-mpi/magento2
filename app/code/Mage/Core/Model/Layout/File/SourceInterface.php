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
interface Mage_Core_Model_Layout_File_SourceInterface
{
    /**
     * Retrieve instances of layout files
     *
     * @param Mage_Core_Model_ThemeInterface $theme Theme that defines the design context
     * @return Mage_Core_Model_Layout_File[]
     */
    public function getFiles(Mage_Core_Model_ThemeInterface $theme);
}
