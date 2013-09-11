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
 * Theme customization interface
 */
namespace Magento\Core\Model\Theme;

interface CustomizationInterface
{
    /**
     * Retrieve list of files which belong to a theme
     *
     * @return \Magento\Core\Model\Theme\Customization\FileInterface[]
     */
    public function getFiles();

    /**
     * Retrieve list of files which belong to a theme only by type
     *
     * @param string $type
     * @return \Magento\Core\Model\Theme\Customization\FileInterface[]
     */
    public function getFilesByType($type);

    /**
     * Returns customization absolute path
     *
     * @return string
     */
    public function getCustomizationPath();

    /**
     * Get directory where themes files are stored
     *
     * @return string
     */
    public function getThemeFilesPath();

    /**
     * Get path to custom view configuration file
     *
     * @return string
     */
    public function getCustomViewConfigPath();

    /**
     * Reorder files positions
     *
     * @param string $type
     * @param array $sequence
     * @return $this
     */
    public function reorder($type, array $sequence);

    /**
     * Remove custom files by ids
     *
     * @param array $fileIds
     * @return $this
     */
    public function delete(array $fileIds);
}
