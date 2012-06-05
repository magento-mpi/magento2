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
class Mage_Core_Model_Design_Fallback
{
    /**
     * Get existing file name using fallback mechanism
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $area, $package, $theme, $module = null)
    {
        $dir = Mage::getBaseDir('design');
        $dirs = array();
        while ($theme) {
            $dirs[] = "{$dir}/{$area}/{$package}/{$theme}";
            list($package, $theme) = $this->getInheritedTheme($area, $package, $theme);
        }

        $moduleDir = $module ? array(Mage::getConfig()->getModuleDir('view', $module) . "/{$area}") : array();
        return $this->_fallback($file, $dirs, $module, $moduleDir);
    }

    /**
     * Get locale file name using fallback mechanism
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string $locale
     * @return string
     */
    public function getLocaleFile($file, $area, $package, $theme, $locale)
    {
        $dir = Mage::getBaseDir('design');
        $dirs = array();
        do {
            $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/locale/{$locale}";
            list($package, $theme) = $this->getInheritedTheme($area, $package, $theme);
        } while ($theme);

        return $this->_fallback($file, $dirs);
    }

    /**
     * Get skin file name using fallback mechanism
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string $skin
     * @param string $locale
     * @param string|null $module
     * @return string
     */
    public function getSkinFile($file, $area, $package, $theme, $skin, $locale, $module = null)
    {
        $dir = Mage::getBaseDir('design');
        $moduleDir = $module ? Mage::getConfig()->getModuleDir('view', $module) : '';
        $defaultSkin = Mage_Core_Model_Design_Package::DEFAULT_SKIN_NAME;

        $dirs = array();
        while ($theme) {
            $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$skin}/locale/{$locale}";
            $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$skin}";
            if ($skin != $defaultSkin) {
                $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$defaultSkin}/locale/{$locale}";
                $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$defaultSkin}";
            }
            list($package, $theme) = $this->getInheritedTheme($area, $package, $theme);
        }

        return $this->_fallback(
            $file,
            $dirs,
            $module,
            array("{$moduleDir}/{$area}/locale/{$locale}", "{$moduleDir}/{$area}"),
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
     * If the specified theme does not inherit other theme the result is false.
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @return array|false
     */
    public function getInheritedTheme($area, $package, $theme)
    {
        $parentTheme = Mage::getDesign()->getThemeConfig($area)->getParentTheme($package, $theme);
        if (!$parentTheme) {
            return false;
        }
        $result = explode('/', $parentTheme, 2);
        if (count($result) > 1) {
            return $result;
        }
        return array($package, $parentTheme);
    }
}
