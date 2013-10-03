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
namespace Magento\Core\Model\Theme\Customization;

class Path
{
    /**
     * Customization directory name
     */
    const DIR_NAME = 'theme_customization';

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Core\Model\Dir $dir
     */
    public function __construct(\Magento\Core\Model\Dir $dir)
    {
        $this->_dir = $dir;
    }

    /**
     * Returns customization absolute path
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return string|null
     */
    public function getCustomizationPath(\Magento\Core\Model\Theme $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->_dir->getDir(\Magento\Core\Model\Dir::MEDIA)
                . DIRECTORY_SEPARATOR . self::DIR_NAME
                . DIRECTORY_SEPARATOR . $theme->getId();
        }
        return $path;
    }

    /**
     * Get directory where themes files are stored
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return string|null
     */
    public function getThemeFilesPath(\Magento\Core\Model\Theme $theme)
    {
        $path = null;
        if ($theme->getFullPath()) {
            $physicalThemesDir = $this->_dir->getDir(\Magento\Core\Model\Dir::THEMES);
            $path = \Magento\Filesystem::fixSeparator($physicalThemesDir . DIRECTORY_SEPARATOR . $theme->getFullPath());
        }
        return $path;
    }

    /**
     * Get path to custom view configuration file
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return string|null
     */
    public function getCustomViewConfigPath(\Magento\Core\Model\Theme $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->getCustomizationPath($theme) . DIRECTORY_SEPARATOR
                . \Magento\Core\Model\Theme::FILENAME_VIEW_CONFIG;
        }
        return $path;
    }
}
