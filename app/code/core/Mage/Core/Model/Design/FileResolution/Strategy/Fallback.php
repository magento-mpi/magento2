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
 * Resolver, which performs full search of files, according to fallback rules
 */
class Mage_Core_Model_Design_FileResolution_Strategy_Fallback
    implements Mage_Core_Model_Design_FileResolution_Strategy_FileInterface,
    Mage_Core_Model_Design_FileResolution_Strategy_LocaleInterface,
    Mage_Core_Model_Design_FileResolution_Strategy_ViewInterface
{
    /**
     * Constructor.
     * Following entries in $params are required: 'area', 'themeModel', 'locale'. The 'appConfig' and
     * 'themeConfig' may contain application config and theme config, respectively. If these these entries are not
     * present or null, then they will be retrieved from global application instance.
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Dir $dirs
    ) {
        $this->_dirs = $dirs;
        $this->_objectManager = $objectManager;
        $this->_filesystem = $filesystem;
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($area, Mage_Core_Model_Theme $themeModel, $file, $module = null)
    {
        $dir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        $dirs = array();
        $currentThemeModel = $themeModel;
        while ($currentThemeModel) {
            $themePath = $currentThemeModel->getThemePath();
            if ($themePath) {
                $dirs[] = "{$dir}/{$area}/{$themePath}";
            }
            $currentThemeModel = $currentThemeModel->getParentTheme();
        }

        if ($module) {
            $moduleDir = array($this->_objectManager->get('Mage_Core_Model_Config')->getModuleDir('view', $module)
                . "/{$area}");
        } else {
            $moduleDir = array();
        }
        return $this->_fallback($file, $dirs, $module, $moduleDir);
    }

    /**
     * Get locale file name, using fallback mechanism
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @return string
     */
    public function getLocaleFile($area, Mage_Core_Model_Theme $themeModel, $locale, $file)
    {
        $dir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        $dirs = array();
        $currentThemeModel = $themeModel;
        while ($currentThemeModel) {
            $themePath = $currentThemeModel->getThemePath();
            if ($themePath) {
                $dirs[] = "{$dir}/{$area}/{$themePath}/locale/{$locale}";
            }
            $currentThemeModel = $currentThemeModel->getParentTheme();
        }

        return $this->_fallback($file, $dirs);
    }

    /**
     * Get theme file name, using fallback mechanism
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, Mage_Core_Model_Theme $themeModel, $locale, $file, $module = null)
    {
        $dir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
        $moduleDir = $module ? $this->_objectManager->get('Mage_Core_Model_Config')->getModuleDir('view', $module) : '';

        $dirs = array();
        $currentThemeModel = $themeModel;
        while ($currentThemeModel) {
            $themePath = $currentThemeModel->getThemePath();
            if ($themePath) {
                $dirs[] = "{$dir}/{$area}/{$themePath}/locale/{$locale}";
                $dirs[] = "{$dir}/{$area}/{$themePath}";
            }
            $currentThemeModel = $currentThemeModel->getParentTheme();
        }

        return $this->_fallback(
            $file,
            $dirs,
            $module,
            array("{$moduleDir}/{$area}/locale/{$locale}", "{$moduleDir}/{$area}"),
            array($this->_dirs->getDir(Mage_Core_Model_Dir::PUB_LIB))
        );
    }

    /**
     * Go through specified directories and try to locate the file
     *
     * Returns the first found file or last file from the list as absolute path
     *
     * @param string $file relative file name
     * @param array $themeDirs theme directories (absolute paths) - must not be empty
     * @param string|bool $module module context
     * @param array $moduleDirs module directories (absolute paths, makes sense with previous parameter only)
     * @param array $extraDirs additional lookup directories (absolute paths)
     * @return string
     */
    protected function _fallback($file, $themeDirs, $module = false, $moduleDirs = array(), $extraDirs = array())
    {
        // add modules to lookup
        $dirs = $themeDirs;
        if ($module) {
            array_walk($themeDirs, function (&$dir) use ($module) {
                $dir = "{$dir}/{$module}";
            });
            $dirs = array_merge($dirs, $themeDirs, $moduleDirs);
        }
        $dirs = array_merge($dirs, $extraDirs);

        // look for files
        $tryFile = '';
        foreach ($dirs as $dir) {
            $tryFile = str_replace('/', DIRECTORY_SEPARATOR, "{$dir}/{$file}");
            if ($this->_filesystem->has($tryFile)) {
                break;
            }
        }
        return $tryFile;
    }
}
