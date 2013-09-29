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
 * Theme file interface
 */
namespace Magento\Core\Model\Theme;

interface FileInterface
{
    /**
     * Set customization service model
     *
     * @param \Magento\Core\Model\Theme\Customization\FileInterface $service
     * @return $this
     */
    public function setCustomizationService(\Magento\Core\Model\Theme\Customization\FileInterface $service);

    /**
     * Get customization service model
     *
     * @return \Magento\Core\Model\Theme\Customization\FileInterface
     */
    public function getCustomizationService();

    /**
     * Attaches selected theme to current file
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return $this
     */
    public function setTheme(\Magento\Core\Model\Theme $theme);

    /**
     * Get theme model
     *
     * @return \Magento\Core\Model\Theme
     */
    public function getTheme();

    /**
     * Set filename of custom file
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);

    /**
     * Get filename of custom file
     *
     * @return string|null
     */
    public function getFileName();

    /**
     * Return absolute path to file of customization
     *
     * @return string
     */
    public function getFullPath();

    /**
     * Get short file information which can be serialized
     *
     * @return array
     */
    public function getFileInfo();

    /**
     * Get content of current file
     *
     * @return string
     */
    public function getContent();

    /**
     * Save custom file
     *
     * @return $this
     */
    public function save();

    /**
     * Delete custom file
     *
     * @return $this
     */
    public function delete();
}
