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
 * Class for managing fallback of files
 */
class Mage_Core_Model_Design_Fallback implements Mage_Core_Model_Design_FallbackInterface
{
    /**
     * @var string
     */
    protected $_area;

    /**
     * @var string
     */
    protected $_package;

    /**
     * @var string
     */
    protected $_theme;

    /**
     * @var string|null
     */
    protected $_skin;

    /**
     * @var string|null
     */
    protected $_locale;

    /**
     * Constructor.
     * Following entries in $params are required: 'area', 'package', 'theme', 'skin', 'locale'
     *
     * @param array $params
     */
    public function __construct($params)
    {
        $this->_area = $params['area'];
        $this->_package = $params['package'];
        $this->_theme = $params['theme'];
        $this->_skin = $params['skin'];
        $this->_locale = $params['locale'];
    }

    /**
     * Get existing file name, using fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $module = null)
    {
        $dir = Mage::getBaseDir('design');
        $dirs = array();
        $theme = $this->_theme;
        $package = $this->_package;
        while ($theme) {
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}";
            list($package, $theme) = $this->_getInheritedTheme($package, $theme);
        }

        $moduleDir = $module ? array(Mage::getConfig()->getModuleDir('view', $module) . "/{$this->_area}") : array();
        return $this->_fallback($file, $dirs, $module, $moduleDir);
    }

    /**
     * Get locale file name, using fallback mechanism
     *
     * @param string $file
     * @return string
     */
    public function getLocaleFile($file)
    {
        $dir = Mage::getBaseDir('design');
        $dirs = array();
        $package = $this->_package;
        $theme = $this->_theme;
        do {
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}/locale/{$this->_locale}";
            list($package, $theme) = $this->_getInheritedTheme($package, $theme);
        } while ($theme);

        return $this->_fallback($file, $dirs);
    }

    /**
     * Get skin file name, using fallback mechanism
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getSkinFile($file, $module = null)
    {
        $dir = Mage::getBaseDir('design');
        $moduleDir = $module ? Mage::getConfig()->getModuleDir('view', $module) : '';
        $defaultSkin = Mage_Core_Model_Design_Package::DEFAULT_SKIN_NAME;

        $dirs = array();
        $theme = $this->_theme;
        $package = $this->_package;
        while ($theme) {
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}/skin/{$this->_skin}/locale/{$this->_locale}";
            $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}/skin/{$this->_skin}";
            if ($this->_skin != $defaultSkin) {
                $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}/skin/{$defaultSkin}/locale/{$this->_locale}";
                $dirs[] = "{$dir}/{$this->_area}/{$package}/{$theme}/skin/{$defaultSkin}";
            }
            list($package, $theme) = $this->_getInheritedTheme($package, $theme);
        }

        return $this->_fallback(
            $file,
            $dirs,
            $module,
            array("{$moduleDir}/{$this->_area}/locale/{$this->_locale}", "{$moduleDir}/{$this->_area}"),
            array(Mage::getBaseDir('js'))
        );
    }

    /**
     * Go through specified directories and try to locate the file
     *
     * Returns the first found file or last file from the list as absolute path
     *
     * @param string $file relative file name
     * @param array $themeDirs theme directories (absolute paths) - must not be empty
     * @param string|false $module module context
     * @param array $moduleDirs module directories (absolute paths, makes sense with previous parameter only)
     * @param array $extraDirs additional lookup directories (absolute paths)
     * @return string
     */
    protected function _fallback($file, $themeDirs, $module = false, $moduleDirs = array(), $extraDirs = array())
    {
        Magento_Profiler::start(__METHOD__);
        // add modules to lookup
        $dirs = $themeDirs;
        if ($module) {
            array_walk($themeDirs, function(&$dir) use ($module) {
                $dir = "{$dir}/{$module}";
            });
            $dirs = array_merge($themeDirs, $moduleDirs);
        }
        $dirs = array_merge($dirs, $extraDirs);
        // look for files
        $tryFile = '';
        foreach ($dirs as $dir) {
            $tryFile = str_replace('/', DIRECTORY_SEPARATOR, "{$dir}/{$file}");
            if (file_exists($tryFile)) {
                break;
            }
        }

        Magento_Profiler::stop(__METHOD__);
        return $tryFile;
    }

    /**
     * Get the name of the inherited theme
     *
     * If the specified theme inherits other theme the result is the name of inherited theme.
     * If the specified theme does not inherit other theme the result is null.
     *
     * @param string $package
     * @param string $theme
     * @return string|null
     */
    protected function _getInheritedTheme($package, $theme)
    {
        return Mage::getDesign()->getThemeConfig($this->_area)->getParentTheme($package, $theme);
    }

    /**
     * Object notified, that skin file was published, thus it can return published file name on next calls
     *
     * @param string $publicFilePath
     * @param string $file
     * @param string|null $module
     * @return Mage_Core_Model_Design_FallbackInterface
     */
    public function notifySkinFilePublished($publicFilePath, $file, $module = null)
    {
        // Do nothing - we don't cache file paths in real fallback
        return $this;
    }
}
