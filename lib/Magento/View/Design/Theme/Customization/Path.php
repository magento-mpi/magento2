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
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $mediaDirectoryRead;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $themeDirectoryRead;

    /**
     * Constructor
     *
     * @param \Magento\Filesystem $filesystem
     * @param $filename
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        $filename = \Magento\View\ConfigInterface::CONFIG_FILE_NAME
    ) {
        $this->filesystem           = $filesystem;
        $this->filename             = $filename;
        $this->mediaDirectoryRead   = $this->filesystem->getDirectoryRead(\Magento\Filesystem\DirectoryList::MEDIA);
        $this->themeDirectoryRead   = $this->filesystem->getDirectoryRead(\Magento\Filesystem\DirectoryList::THEMES);
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
            $path = $this->mediaDirectoryRead->getAbsolutePath(self::DIR_NAME . $theme->getId());
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
            $path = $this->themeDirectoryRead->getAbsolutePath($theme->getFullPath());
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
            $path = $this->mediaDirectoryRead
                ->getAbsolutePath(self::DIR_NAME . $theme->getId() . '/' . $this->filename);

        }
        return $path;
    }
}
