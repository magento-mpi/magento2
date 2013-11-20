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
     * File name
     *
     * @var string
     */
    protected $filename;

    /**
     * Filesystem instance
     *
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Filesystem $filesystem
     * @param $filename
     */
    public function __construct(\Magento\Filesystem $filesystem, $filename = \Magento\View\ConfigInterface::CONFIG_FILE_NAME)
    {
        $this->filesystem = $filesystem;
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
            $path = $this->filesystem->getPath(\Magento\Filesystem\DirectoryList::MEDIA)
                . '/' . self::DIR_NAME
                . '/' . $theme->getId();
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
            $physicalThemesDir = $this->filesystem->getPath(\Magento\Filesystem\DirectoryList::THEMES);
            $path = str_replace('\\', '/', $physicalThemesDir . '/' . $theme->getFullPath());
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
            $path = $this->getCustomizationPath($theme) . '/' . $this->filename;
        }
        return $path;
    }
}
