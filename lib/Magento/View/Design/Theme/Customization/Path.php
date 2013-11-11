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
namespace Magento\View\Design\Theme\Customization;

class Path
{
    /**
     * Customization directory name
     */
    const DIR_NAME = 'theme_customization';

    /**
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @var string
     */
    protected $filename;

    /**
     * Initialize dependencies
     *
     * @param \Magento\App\Dir $dir
     * @param $filename
     */
    public function __construct(\Magento\App\Dir $dir, $filename = \Magento\View\ConfigInterface::CONFIG_FILE_NAME)
    {
        $this->_dir = $dir;
        $this->filename = $filename;
    }

    /**
     * Returns customization absolute path
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return string|null
     */
    public function getCustomizationPath(\Magento\View\Design\ThemeInterface $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->_dir->getDir(\Magento\App\Dir::MEDIA)
                . DIRECTORY_SEPARATOR . self::DIR_NAME
                . DIRECTORY_SEPARATOR . $theme->getId();
        }
        return $path;
    }

    /**
     * Get directory where themes files are stored
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return string|null
     */
    public function getThemeFilesPath(\Magento\View\Design\ThemeInterface $theme)
    {
        $path = null;
        if ($theme->getFullPath()) {
            $physicalThemesDir = $this->_dir->getDir(\Magento\App\Dir::THEMES);
            $path = \Magento\Filesystem::fixSeparator($physicalThemesDir . DIRECTORY_SEPARATOR . $theme->getFullPath());
        }
        return $path;
    }

    /**
     * Get path to custom view configuration file
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return string|null
     */
    public function getCustomViewConfigPath(\Magento\View\Design\ThemeInterface $theme)
    {
        $path = null;
        if ($theme->getId()) {
            $path = $this->getCustomizationPath($theme) . DIRECTORY_SEPARATOR . $this->filename;
        }
        return $path;
    }
}
