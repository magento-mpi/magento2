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
 * Theme Customization Path
 */
class Magento_Core_Model_Theme_Customization_Path
{
    /**
     * Customization directory name
     */
    const DIR_NAME = 'theme_customization';

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Initialize dependencies
     *
     * @param Magento_Core_Model_Dir $dir
     */
    public function __construct(Magento_Core_Model_Dir $dir)
    {
        $this->_dir = $dir;
    }

    /**
     * Returns customization absolute path
     *
     * @param Magento_Core_Model_Theme $theme
     * @return string|null
     */
    public function getCustomizationPath(Magento_Core_Model_Theme $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->_dir->getDir(Magento_Core_Model_Dir::MEDIA)
                . DIRECTORY_SEPARATOR . self::DIR_NAME
                . DIRECTORY_SEPARATOR . $theme->getId();
        }
        return $path;
    }

    /**
     * Get directory where themes files are stored
     *
     * @param Magento_Core_Model_Theme $theme
     * @return string|null
     */
    public function getThemeFilesPath(Magento_Core_Model_Theme $theme)
    {
        $path = null;
        if ($theme->getFullPath()) {
            $physicalThemesDir = $this->_dir->getDir(Magento_Core_Model_Dir::THEMES);
            $path = Magento_Filesystem::fixSeparator($physicalThemesDir . DIRECTORY_SEPARATOR . $theme->getFullPath());
        }
        return $path;
    }

    /**
     * Get path to custom view configuration file
     *
     * @param Magento_Core_Model_Theme $theme
     * @return string|null
     */
    public function getCustomViewConfigPath(Magento_Core_Model_Theme $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->getCustomizationPath($theme) . DIRECTORY_SEPARATOR
                . Magento_Core_Model_Theme::FILENAME_VIEW_CONFIG;
        }
        return $path;
    }
}
