<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme;

/**
 * Theme file interface
 */
interface FileInterface
{
    /**
     * Set customization service model
     *
     * @param \Magento\View\Design\Theme\Customization\FileInterface $service
     * @return $this
     */
    public function setCustomizationService(Customization\FileInterface $service);

    /**
     * Get customization service model
     *
     * @return \Magento\View\Design\Theme\Customization\FileInterface
     */
    public function getCustomizationService();

    /**
     * Attaches selected theme to current file
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return $this
     */
    public function setTheme(\Magento\View\Design\ThemeInterface $theme);

    /**
     * Get theme model
     *
     * @return \Magento\View\Design\ThemeInterface
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
