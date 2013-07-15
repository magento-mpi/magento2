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
 * Theme Customization Path
 */
class Mage_Core_Model_Theme_Customization_Path
{
    /**
     * Customization directory name
     */
    const DIR_NAME = 'theme_customization';

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(Mage_Core_Model_Dir $dir)
    {
        $this->_dir = $dir;
    }

    /**
     * Returns customization absolute path
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string|null
     */
    public function getCustomizationPath(Mage_Core_Model_Theme $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->_dir->getDir(Mage_Core_Model_Dir::MEDIA)
                . DIRECTORY_SEPARATOR . self::DIR_NAME
                . DIRECTORY_SEPARATOR . $theme->getId();
        }
        return $path;
    }

    /**
     * Get directory where themes files are stored
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string|null
     */
    public function getThemeFilesPath(Mage_Core_Model_Theme $theme)
    {
        $path = null;
        if ($theme->getFullPath()) {
            $physicalThemesDir = $this->_dir->getDir(Mage_Core_Model_Dir::THEMES);
            $path = Magento_Filesystem::fixSeparator($physicalThemesDir . DIRECTORY_SEPARATOR . $theme->getFullPath());
        }
        return $path;
    }

    /**
     * Get path to custom view configuration file
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string|null
     */
    public function getCustomViewConfigPath(Mage_Core_Model_Theme $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->getCustomizationPath($theme) . DIRECTORY_SEPARATOR
                . Mage_Core_Model_Theme::FILENAME_VIEW_CONFIG;
        }
        return $path;
    }
}
