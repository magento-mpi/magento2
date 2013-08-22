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
interface Magento_Core_Model_Theme_FileInterface
{
    /**
     * Set customization service model
     *
     * @param Magento_Core_Model_Theme_Customization_FileInterface $service
     * @return $this
     */
    public function setCustomizationService(Magento_Core_Model_Theme_Customization_FileInterface $service);

    /**
     * Get customization service model
     *
     * @return Magento_Core_Model_Theme_Customization_FileInterface
     */
    public function getCustomizationService();

    /**
     * Attaches selected theme to current file
     *
     * @param Magento_Core_Model_Theme $theme
     * @return $this
     */
    public function setTheme(Magento_Core_Model_Theme $theme);

    /**
     * Get theme model
     *
     * @return Magento_Core_Model_Theme
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
