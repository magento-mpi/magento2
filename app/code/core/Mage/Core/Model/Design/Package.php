<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Design_Package
{
    const DEFAULT_AREA    = 'frontend';
    const DEFAULT_PACKAGE = 'default';
    const DEFAULT_THEME   = 'default';

    const SCOPE_SEPARATOR = '::';

    const PUBLIC_MERGE_DIR  = '_merged';
    const PUBLIC_MODULE_DIR = '_module';

    const CONTENT_TYPE_CSS = 'css';
    const CONTENT_TYPE_JS  = 'js';

    const STATIC_TYPE_LIB  = 'lib';
    const STATIC_TYPE_SKIN = 'skin';

    /**
     * The name of the default skins in the context of a theme
     */
    const DEFAULT_SKIN_NAME = 'default';

    /**
     * The name of the default theme in the context of a package
     */
    const DEFAULT_THEME_NAME = 'default';

    /**
     * Published file cache storage tag
     */
    const PUBLIC_CACHE_TAG = 'design_public';

    private static $_regexMatchCache      = array();
    private static $_customThemeTypeCache = array();

    /**
     * Current Store for generation ofr base_dir and base_url
     *
     * @var string|integer|Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Package area
     *
     * @var string
     */
    protected $_area;

    /**
     * Package name
     *
     * @var string
     */
    protected $_name;

    /**
     * Package theme
     *
     * @var string
     */
    protected $_theme;

    /**
     * @var string
     */
    protected $_skin;

    /**
     * Package root directory
     *
     * @var string
     */
    protected $_rootDir;

    /**
     * Directory of the css file
     * Using only to transmit additional parametr in callback functions
     * @var string
     */
    protected $_callbackFileDir;

    protected $_config = null;

    /**
     * Published file cache storages
     *
     * @var array
     */
    protected $_publicCache = array();

    /**
     * Set store
     *
     * @param  string|integer|Mage_Core_Model_Store $store
     * @return Mage_Core_Model_Design_Package
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return string|integer|Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            return Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Set package area
     *
     * @param  string $area
     * @return Mage_Core_Model_Design_Package
     */
    public function setArea($area)
    {
        $this->_area = $area;
        return $this;
    }

    /**
     * Retrieve package area
     *
     * @return unknown
     */
    public function getArea()
    {
        if (is_null($this->_area)) {
            $this->_area = self::DEFAULT_AREA;
        }
        return $this->_area;
    }

    /**
     * Set package name
     * In case of any problem, the default will be set.
     *
     * @param  string $name
     * @return Mage_Core_Model_Design_Package
     */
    public function setPackageName($name = '')
    {
        if (empty($name)) {
            // see, if exceptions for user-agents defined in config
            $customPackage = $this->_checkUserAgentAgainstRegexps('design/package/ua_regexp');
            if ($customPackage) {
                $this->_name = $customPackage;
            }
            else {
                $this->_name = Mage::getStoreConfig('design/package/name', $this->getStore());
            }
        }
        else {
            $this->_name = $name;
        }
        // make sure not to crash, if wrong package specified
        if (!$this->designPackageExists($this->_name, $this->getArea())) {
            $this->_name = self::DEFAULT_PACKAGE;
        }
        return $this;
    }

    /**
     * Set store/package/area at once, and get respective values, that were before
     *
     * $storePackageArea must be assoc array. The keys may be:
     * 'store', 'package', 'area'
     *
     * @param array $storePackageArea
     * @return array
     */
    public function setAllGetOld($storePackageArea)
    {
        $oldValues = array();
        if (array_key_exists('store', $storePackageArea)) {
            $oldValues['store'] = $this->getStore();
            $this->setStore($storePackageArea['store']);
        }
        if (array_key_exists('area', $storePackageArea)) {
            $oldValues['area'] = $this->getArea();
            $this->setArea($storePackageArea['area']);
        }
        if (array_key_exists('package', $storePackageArea)) {
            $oldValues['package'] = $this->getPackageName();
            $this->setPackageName($storePackageArea['package']);
        }
        return $oldValues;
    }

    /**
     * Retrieve package name
     *
     * @return string
     */
    public function getPackageName()
    {
        if (null === $this->_name) {
            $this->setPackageName();
        }
        return $this->_name;
    }

    public function designPackageExists($packageName, $area = self::DEFAULT_AREA)
    {
        return is_dir(Mage::getBaseDir('design') . DS . $area . DS . $packageName);
    }

    /**
     * Declare design package theme params
     * Polymorph method:
     * 1) if 1 parameter specified, sets everything to this value
     * 2) if 2 parameters, treats 1st as key and 2nd as value
     *
     * @return Mage_Core_Model_Design_Package
     */
    public function setTheme()
    {
        switch (func_num_args()) {
            case 1:
                foreach (array('layout', 'template', 'locale') as $type) {
                    $this->_theme[$type] = func_get_arg(0);
                }
                $this->setSkin(func_get_arg(0));
                break;

            case 2:
                $this->_theme[func_get_arg(0)] = func_get_arg(1);
                break;

            default:
                throw Mage::exception(Mage::helper('core')->__('Wrong number of arguments for %s', __METHOD__));
        }
        return $this;
    }

    /**
     * Package theme getter
     *
     * @param string $type
     * @return string
     */
    public function getTheme($type)
    {
        if (empty($this->_theme[$type])) {
            $this->_theme[$type] = Mage::getStoreConfig('design/theme/'.$type, $this->getStore());
            if ($type!=='default' && empty($this->_theme[$type])) {
                $this->_theme[$type] = $this->getTheme('default');
                if (empty($this->_theme[$type])) {
                    $this->_theme[$type] = self::DEFAULT_THEME;
                }

                // "locale", "layout", "template"
            }
        }

        // + "default", "skin"

        // set exception value for theme, if defined in config
        $customThemeType = $this->_checkUserAgentAgainstRegexps("design/theme/{$type}_ua_regexp");
        if ($customThemeType) {
            $this->_theme[$type] = $customThemeType;
        }

        return $this->_theme[$type];
    }

    public function getDefaultTheme()
    {
        return self::DEFAULT_THEME;
    }

    /**
     * Skin setter
     *
     * @param string $skin
     * @return Mage_Core_Model_Design_Package
     */
    public function setSkin($skin)
    {
        $this->_skin = $skin;
        return $this;
    }

    /**
     * Skin getter
     *
     * @return string
     */
    public function getSkin()
    {
        if (!$this->_skin) {
            $this->_skin = self::DEFAULT_SKIN_NAME;
        }
        return $this->_skin;
    }

    /**
     * Update required parameters with default values if custom not specified
     *
     * @param array $params
     * @return Mage_Core_Model_Design_Package
     */
    protected function _updateParamDefaults(array &$params)
    {
        if ($this->getStore()) {
            $params['_store'] = $this->getStore();
        }
        if (empty($params['_area'])) {
            $params['_area'] = $this->getArea();
        }
        if (empty($params['_package'])) {
            $params['_package'] = $this->getPackageName();
        }
        if (!array_key_exists('_theme', $params)) {
            $params['_theme'] = $this->getTheme( (isset($params['_type'])) ? $params['_type'] : '' );
        }
        if (!array_key_exists('_skin', $params)) {
            $params['_skin'] = $this->getSkin();
        }
        if (empty($params['_default'])) {
            $params['_default'] = false;
        }
        if (!array_key_exists('_module', $params)) {
            $params['_module'] = false;
        }
        $params['_locale'] = Mage::app()->getLocale()->getLocaleCode();
        return $this;
    }

    /**
     * @todo replace method usage with getFilename (MAGETWO-521)
     */
    public function getBaseDir(array $params)
    {
        $this->_updateParamDefaults($params);
        $baseDir = (empty($params['_relative']) ? Mage::getBaseDir('design').DS : '').
            $params['_area'].DS.$params['_package'].DS.$params['_theme'].DS.$params['_type'];
        return $baseDir;
    }

    /**
     * @todo replace method usage with getSkinFile/getSkinUrl (MAGETWO-521)
     */
    public function getSkinBaseDir(array $params=array())
    {
        $params['_type'] = 'skin';
        $this->_updateParamDefaults($params);
        $baseDir = (empty($params['_relative']) ? Mage::getBaseDir('skin').DS : '').
            $params['_area'].DS.$params['_package'].DS.$params['_theme'];
        return $baseDir;
    }

    /**
     * Go through specified directories and try to locate the file
     *
     * Returns the first found file or last file from the list as absolute path
     *
     * @param string $file relative file name
     * @param array $themeDirs list of theme directories (absolute paths) - must not be empty
     * @param string|false $module module context
     * @param string $moduleDirs list of module directories (absolute paths, makes sense with previous parameter only)
     * @return string
     */
    protected function _fallback($file, $themeDirs, $module, $moduleDirs)
    {
        Magento_Profiler::start(__METHOD__);
        // add modules to lookup
        $dirs = $themeDirs;
        if ($module) {
            /*
            array_walk($themeDirs, function(&$dir) use ($module) {
                $dir = "{$dir}/{$module}";
            });*/
            /* Legacy code that not replace lookup dirs. After migration uncomment code above and remove foreach*/
            $dirs = array();
            foreach ($themeDirs as $dir) {
                $dirs[] = "{$dir}/{$module}";
                $dirs[] = $dir;
            }
            $themeDirs = $dirs;
            $dirs = array_merge($themeDirs, $moduleDirs);
        }
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
     * Use this one to get existing file name with fallback to default
     *
     * $params['_type'] is required
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params)
    {
        Magento_Profiler::start(__METHOD__);
        $file = $this->_extractScope($file, $params);
        $this->_updateParamDefaults($params);

        $dir = Mage::getBaseDir('design');
        $dirs = array();
        $area = $params['_area'];
        $theme = $params['_theme'];
        $module = $params['_module'];

        do {
            $dirs[] = "{$dir}/{$area}/{$params['_package']}/{$theme}";
            /* Legacy path that should be removed after all template and layout files relocation */
            $dirs[] = "{$dir}/{$area}/{$params['_package']}/{$theme}/{$params['_type']}";
            $theme = $this->_getInheritedTheme($theme);
        } while ($theme);
        /* Legacy path that should be removed after all template and layout files relocation */
        $dirs[] = "{$dir}/{$area}/base/default/{$params['_type']}";

        $moduleDir = $module ? array(Mage::getConfig()->getModuleDir('view', $module) . "/{$area}") : array();
        Magento_Profiler::stop(__METHOD__);
        return $this->_fallback($file, $dirs, $module, $moduleDir);
    }

    /**
     * Identify file scope if it defined in file name and override _module parameter in $params array
     *
     * @param string $file
     * @param array &$params
     * @return string
     */
    protected function _extractScope($file, array &$params)
    {
        if (preg_match('/\.\//', str_replace('\\', '/', $file))) {
            throw new Exception("File name '{$file}' is forbidden for security reasons.");
        }
        if (false !== strpos($file, self::SCOPE_SEPARATOR)) {
            $file = explode(self::SCOPE_SEPARATOR, $file);
            if (empty($file[0])) {
                throw new Exception('Scope separator "::" can\'n be used without scope identifier.');
            }
            $params['_module'] = $file[0];
            $file = $file[1];
        }
        return $file;
    }

    /**
     * Path getter for layout file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLayoutFilename($file, array $params=array())
    {
        $params['_type'] = 'layout';
        return $this->getFilename($file, $params);
    }

    /**
     * Path getter for template file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getTemplateFilename($file, array $params=array())
    {
        $params['_type'] = 'template';
        return $this->getFilename($file, $params);
    }

    /**
     * Path getter for locale file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLocaleFileName($file, array $params=array())
    {
        $this->_updateParamDefaults($params);
        $dir = Mage::getBaseDir('design');
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $dirs = array();
        do {
            $dirs[] = "{$dir}/{$params['_area']}/{$params['_package']}/{$params['_theme']}/locale/{$locale}";
            $theme = $this->_getInheritedTheme($params['_theme']);
        } while ($theme);

        return $this->_fallback($file, $dirs, false, array());
    }

    /**
     * Design packages list getter
     *
     * @return array
     */
    public function getPackageList()
    {
        $directory = Mage::getBaseDir('design') . DS . 'frontend';
        return $this->_listDirectories($directory);
    }

    /**
     * Design package (optional) themes list getter
     * @param string $package
     * @return string
     */
    public function getThemeList($package = null)
    {
        $result = array();

        if (is_null($package)){
            foreach ($this->getPackageList() as $package){
                $result[$package] = $this->getThemeList($package);
            }
        } else {
            $directory = Mage::getBaseDir('design') . DS . 'frontend' . DS . $package;
            $result = $this->_listDirectories($directory);
        }

        return $result;
    }

    /**
     * Directories lister utility method
     *
     * @param string $path
     * @param string|false $fullPath
     * @return array
     */
    private function _listDirectories($path, $fullPath = false)
    {
        $result = array();
        $dir = opendir($path);
        if ($dir) {
            while ($entry = readdir($dir)) {
                if (substr($entry, 0, 1) == '.' || !is_dir($path . DS . $entry)){
                    continue;
                }
                if ($fullPath) {
                    $entry = $path . DS . $entry;
                }
                $result[] = $entry;
            }
            unset($entry);
            closedir($dir);
        }

        return $result;
    }

    /**
     * Get regex rules from config and check user-agent against them
     *
     * Rules must be stored in config as a serialized array(['regexp']=>'...', ['value'] => '...')
     * Will return false or found string.
     *
     * @param string $regexpsConfigPath
     * @return mixed
     */
    protected function _checkUserAgentAgainstRegexps($regexpsConfigPath)
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        if (!empty(self::$_customThemeTypeCache[$regexpsConfigPath])) {
            return self::$_customThemeTypeCache[$regexpsConfigPath];
        }

        $configValueSerialized = Mage::getStoreConfig($regexpsConfigPath, $this->getStore());

        if (!$configValueSerialized) {
            return false;
        }

        $regexps = @unserialize($configValueSerialized);

        if (empty($regexps)) {
            return false;
        }

        return self::getPackageByUserAgent($regexps, $regexpsConfigPath);
    }

    /**
     * Return package name based on design exception rules
     *
     * @param array $rules - design exception rules
     * @param string $regexpsConfigPath
     */
    public static function getPackageByUserAgent(array $rules, $regexpsConfigPath = 'path_mock')
    {
        foreach ($rules as $rule) {
            if (!empty(self::$_regexMatchCache[$rule['regexp']][$_SERVER['HTTP_USER_AGENT']])) {
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }

            $regexp = '/' . trim($rule['regexp'], '/') . '/';

            if (@preg_match($regexp, $_SERVER['HTTP_USER_AGENT'])) {
                self::$_regexMatchCache[$rule['regexp']][$_SERVER['HTTP_USER_AGENT']] = true;
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }
        }

        return false;
    }

    /**
     * Remove all merged js/css files
     *
     * @return  bool
     */
    public function cleanMergedJsCss()
    {
        $dir = $this->_buildPublicSkinFilename(self::PUBLIC_MERGE_DIR);
        $result = Varien_Io_File::rmdirRecursive($dir);
        $result = $result && Mage::helper('core/file_storage_database')->deleteFolder($dir);
        return $result;
    }

    /**
     * Find a skin file using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getSkinFile($file, array $params = array())
    {
        $file = $this->_extractScope($file, $params);
        $this->_updateParamDefaults($params);

        $area = $params['_area'];
        $package = $params['_package'];
        $theme = $params['_theme'];
        $skin = $params['_skin'];
        $module = $params['_module'];
        $dir = Mage::getBaseDir('design');
        $moduleDir = $module ? Mage::getConfig()->getModuleDir('view', $module) : '';
        $defaultSkin = self::DEFAULT_SKIN_NAME;
        $locale = Mage::app()->getLocale()->getLocaleCode();

        $dirs = array();
        do {
            $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$skin}/locale/{$locale}";
            $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$skin}";
            if ($skin != $defaultSkin) {
                $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$defaultSkin}/locale/{$locale}";
                $dirs[] = "{$dir}/{$area}/{$package}/{$theme}/skin/{$defaultSkin}";
            }
            $theme = $this->_getInheritedTheme($theme);
        } while ($theme);

        return $this->_fallback(
            $file,
            $dirs,
            $module,
            array("{$moduleDir}/{$area}/locale/{$locale}", "{$moduleDir}/{$area}",)
        );
    }

    /**
     * Get url to file base on skin file identifier.
     * Publishes file there, if needed.
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getSkinUrl($file, array $params = array())
    {
        $params['_type'] = 'skin';
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        unset($params['_secure']);
        $this->_updateParamDefaults($params);
        /* Identify public file */
        $publicFile = $this->_publishSkinFile($file, $params);
        /* Build url to public file */
        $url = $this->_getPublicFileUrl($publicFile, $isSecure);
        return $url;
    }

    /**
     * Get url to public file
     *
     * @param string $file
     * @param bool|null $isSecure
     * @return string
     */
    protected function _getPublicFileUrl($file, $isSecure = null)
    {
        $publicDir = Mage::getBaseDir('media');
        if (strpos($file, $publicDir) !== 0) {
            throw new Exception('No public access to the file: ' . $file);
        }
        $url = str_replace($publicDir, '', $file);
        $url = ltrim(str_replace(DIRECTORY_SEPARATOR, '/' , $url), '/');
        $url = $this->_getSkinUrl($url, $isSecure);
        return $url;
    }

    /**
     * Composes url to file in skin directory.
     * Just builds the url without doing any publication stuff.
     *
     * @param string $file
     * @param bool|null $isSecure
     * @return string
     */
    protected function _getSkinUrl($file, $isSecure = null)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, $isSecure) . $file;
    }

    /**
     * Get URLs to CSS files optimized based on configuration settings
     *
     * @param array $files files array (array($file => $fileType))
     * @return array
     */
    public function getOptimalCssUrls($files)
    {
        return $this->_getOptimalUrls(
            $files,
            self::CONTENT_TYPE_CSS,
            Mage::getStoreConfigFlag('dev/css/merge_css_files')
        );
    }

    /**
     * Get URLs to JS files optimized based on configuration settings
     *
     * @param array $files files array (array($file => $fileType))
     * @return array
     */
    public function getOptimalJsUrls($files)
    {
        return $this->_getOptimalUrls(
            $files,
            self::CONTENT_TYPE_JS,
            Mage::getStoreConfigFlag('dev/js/merge_files')
        );
    }

    /**
     * Prepare urls to files based on files type and merging option value
     *
     * @param array $files
     * @param string $type
     * @param bool $doMerge
     * @return array
     */
    protected function _getOptimalUrls($files, $type, $doMerge)
    {
        $urls = array();
        if ($doMerge && count($files) > 1) {
            $file = $this->_mergeFiles($files, $type);
            $urls[] = $this->_getPublicFileUrl($file);
        } else {
            foreach ($files as $file => $fileType) {
                if ($fileType == self::STATIC_TYPE_LIB) {
                    $urls[] = $this->getStaticLibUrl($file);
                } else {
                    $urls[] = $this->getSkinUrl($file);
                }
            }
        }
        return $urls;
    }

    /**
     * Check if requested skin file has public access or move it to public folder if necessary
     *
     * @param  string $skinFile
     * @param  array $params
     * @return string
     */
    protected function _publishSkinFile($skinFile, $params)
    {
        $isDuplicationAllowed = (string)Mage::getConfig()->getNode('default/design/theme/allow_skin_files_duplication');
        $skinFile = $this->_extractScope($skinFile, $params);

        $file = $this->getSkinFile($skinFile, $params);
        if (!file_exists($file)) {
            throw new Exception("Unable to locate skin file: '{$file}'");
        }

        $isCssFile = preg_match('/\.css$/', $skinFile);
        if ($isDuplicationAllowed || $isCssFile) {
            $publicFile = $this->_buildPublicSkinRedundantFilename($skinFile, $params);
        } else {
            $publicFile = $this->_buildPublicSkinSufficientFilename($file, $params);
            $this->_setPublicFileIntoCache($skinFile, $params, $publicFile);
        }

        $fileMTime = filemtime($file);
        /* Validate if file not exists or was updated */
        if (!file_exists($publicFile) || $fileMTime != filemtime($publicFile)) {
            $publicDir = dirname($publicFile);
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0777, true);
            }
            /* Process relative urls for CSS files */
            if ($isCssFile) {
                $content = $this->_getPublicCssContent($file, dirname($publicFile), $skinFile, $params);
                file_put_contents($publicFile, $content);
            } else {
                copy($file, $publicFile);
            }
            touch($publicFile, $fileMTime);
        }
        return $publicFile;

    }

    /**
     * Build path to file located in public folder
     *
     * @param string $file
     * @return string
     */
    protected function _buildPublicSkinFilename($file)
    {
        return Mage::getBaseDir('media')  . DIRECTORY_SEPARATOR . 'skin' . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Build public filename for a skin file that always includes area/package/theme/skin/locate parameters
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function _buildPublicSkinRedundantFilename($file, array $params)
    {
        $publicFile = $params['_area']
            . DIRECTORY_SEPARATOR . $params['_package']
            . DIRECTORY_SEPARATOR . $params['_theme']
            . DIRECTORY_SEPARATOR . $params['_skin']
            . DIRECTORY_SEPARATOR . $params['_locale']
            . ($params['_module'] ? DIRECTORY_SEPARATOR . $params['_module'] : '')
            . DIRECTORY_SEPARATOR . $file
        ;
        $publicFile = $this->_buildPublicSkinFilename($publicFile);
        return $publicFile;
    }

    /**
     * Build public filename for a skin file that sufficiently depends on the passed parameters
     *
     * @param string $filename
     * @param array $params
     * @return string
     */
    protected function _buildPublicSkinSufficientFilename($filename, array $params)
    {
        $designDir = Mage::getBaseDir('design') . DIRECTORY_SEPARATOR;
        if (0 === strpos($filename, $designDir)) {
            // theme file
            $publicFile = substr($filename, strlen($designDir));
        } else {
            // modular file
            $module = $params['_module'];
            $moduleDir = Mage::getModuleDir('skin', $module) . DIRECTORY_SEPARATOR;
            $publicFile = substr($filename, strlen($moduleDir));
            $publicFile = self::PUBLIC_MODULE_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $publicFile;
        }
        $publicFile = $this->_buildPublicSkinFilename($publicFile);
        return $publicFile;
    }

    /**
     * Extract non-absolute URLs from a CSS content
     *
     * @param string $cssContent
     * @return array
     */
    protected function _extractCssRelativeUrls($cssContent)
    {
        preg_match_all('#url\([\'"]?(?!http://|https://|/)(.+?)(?:[\#\?].*|[\'"])?\)#', $cssContent, $matches);
        if (!empty($matches[0]) && !empty($matches[1])) {
            return array_combine($matches[0], $matches[1]);
        }
        return array();
    }

    /**
     * Retrieve processed CSS file content that contains URLs relative to the specified public directory
     *
     * @param string $filePath Absolute path to the CSS file
     * @param string $publicDir Absolute path to the public directory to which URLs should be relative
     * @param string $fileName File name used for reference
     * @param array $params Design parameters
     * @return string
     */
    protected function _getPublicCssContent($filePath, $publicDir, $fileName, $params)
    {
        $content = file_get_contents($filePath);
        $relativeUrls = $this->_extractCssRelativeUrls($content);
        foreach ($relativeUrls as $urlNotation => $fileUrl) {
            $relatedFilePathPublic = $this->_publishRelatedSkinFile($fileUrl, $filePath, $fileName, $params);
            $fileUrlNew = basename($relatedFilePathPublic);
            $offset = $this->_getFilesOffset($relatedFilePathPublic, $publicDir);
            if ($offset) {
                $fileUrlNew = $this->_canonize($offset . '/' . $fileUrlNew, true);
            }
            $urlNotationNew = str_replace($fileUrl, $fileUrlNew, $urlNotation);
            $content = str_replace($urlNotation, $urlNotationNew, $content);
        }
        return $content;
    }

    /**
     * Publish relative $fileUrl based on information about parent file path and name
     *
     * @param string $fileUrl URL to the file that was extracted from $parentFilePath
     * @param string $parentFilePath path to the file
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/skin/module parameters array
     * @return string
     */
    protected function _publishRelatedSkinFile($fileUrl, $parentFilePath, $parentFileName, $params)
    {
        if (strpos($fileUrl, self::SCOPE_SEPARATOR)) {
            $relativeSkinFile = $fileUrl;
        } else {
            /* Check if module file overridden on theme level based on _module property and file path */
            if ($params['_module'] && strpos($parentFilePath, Mage::getBaseDir('design')) === 0) {
                /* Add module directory to relative URL for canonization */
                $relativeSkinFile = dirname($params['_module'] . DIRECTORY_SEPARATOR . $parentFileName)
                    . DIRECTORY_SEPARATOR . $fileUrl;
                $relativeSkinFile   = $this->_canonize($relativeSkinFile);
                if (strpos($relativeSkinFile, $params['_module']) === 0) {
                    $relativeSkinFile = str_replace($params['_module'], '', $relativeSkinFile);
                } else {
                    $params['_module'] = false;
                }
            } else {
                $relativeSkinFile = $this->_canonize(dirname($parentFileName) . DIRECTORY_SEPARATOR . $fileUrl);
            }
        }
        return $this->_publishSkinFile($relativeSkinFile, $params);
    }

    /**
     * Get URL for static library file.
     * Usually it is javascript or supplementary file for javascript library.
     *
     * @param string|null $file
     * @param bool|null $isSecure
     * @return string
     */
    public function getStaticLibUrl($file = null, $isSecure = null)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS, $isSecure) . $file;
    }

    /**
     * Canonize the specified filename
     *
     * Removes excessive "./" and "../" from the path.
     * Returns false, if cannot get rid of all "../"
     *
     * @param string $filename
     * @param bool $isRelative flag that identify that filename is relative
     * @throw Exception if file can't be canonized
     * @return string|false
     */
    protected function _canonize($filename, $isRelative = false)
    {
        $result = array();
        $parts = explode('/', str_replace('\\', '/', $filename));
        $prefix = '';
        if ($isRelative) {
            foreach ($parts as $part) {
                if ($part != '..') {
                    break;
                }
                $prefix .= '../';
                array_shift($parts);
            }
        }

        foreach ($parts as $part) {
            if ('..' === $part) {
                if (null === array_pop($result)) {
                    throw new Exception('Invalid file: '.$filename);
                }
            } elseif ('.' !== $part) {
                $result[] = $part;
            }
        }
        return $prefix . implode('/', $result);
    }

    /**
     * Merge files, located under the same folder, into one and return file name of merged file
     *
     * @param array $files list of names relative to the same folder
     * @param string $contentType
     * @throw Exception exception will be triggered if not existing file requested for merge
     * @return string
     */
    protected function _mergeFiles($files, $contentType)
    {
        $filesToMerge = array();
        $mergedFile = array();
        $jsDir = Mage::getBaseDir('js');
        $publicDir = $this->_buildPublicSkinFilename('');
        foreach ($files as $file => $fileType) {
            if ($fileType == self::STATIC_TYPE_LIB) {
                $filesToMerge[$file] = $jsDir . DIRECTORY_SEPARATOR . $file;
                $mergedFile[] = str_replace('\\', '/', str_replace($jsDir, '', $filesToMerge[$file]));
            } else {
                $params = array();
                $this->_updateParamDefaults($params);
                $filesToMerge[$file] = $this->_publishSkinFile($file, $params);
                $mergedFile[] = str_replace('\\', '/', str_replace($publicDir, '', $filesToMerge[$file]));
            }
        }
        $mergedFile = self::PUBLIC_MERGE_DIR . DIRECTORY_SEPARATOR . md5(implode('|', $mergedFile)) . ".{$contentType}";
        $mergedFile = $this->_buildPublicSkinFilename($mergedFile);
        $mergedMTimeFile  = $mergedFile . '.dat';
        $filesMTimeData = '';
        foreach ($filesToMerge as $file) {
            $filesMTimeData .= filemtime($file);
        }
        if (file_exists($mergedFile) && file_exists($mergedMTimeFile)
            && ($filesMTimeData == file_get_contents($mergedMTimeFile))
        ) {
            return $mergedFile;
        }
        if (!is_dir(dirname($mergedFile))) {
            mkdir(dirname($mergedFile), 0777, true);
        }

        $result = array();
        foreach ($filesToMerge as $file) {
            if (!file_exists($file)) {
                throw new Exception("Merging failed: unable to locate file '{$file}'");
            }
            $content = file_get_contents($file);
            if ($contentType == self::CONTENT_TYPE_CSS) {
                $offset = $this->_getFilesOffset($file, dirname($mergedFile));
                $content = $this->_applyCssUrlOffset($content, $offset);
            }
            $result[] = $content;
        }
        $result = ltrim(implode($result));
        if ($contentType == self::CONTENT_TYPE_CSS) {
            $result = $this->_popCssImportsUp($result);
        }
        file_put_contents($mergedFile, $result, LOCK_EX);
        file_put_contents($mergedMTimeFile, $filesMTimeData, LOCK_EX);
        return $mergedFile;
    }

    /**
     * Replace relative URLs in the CSS content with ones shifted by the directories offset
     *
     * @throws Exception
     * @param string $cssContent
     * @param string $relativeOffset
     * @return string
     */
    protected function _applyCssUrlOffset($cssContent, $relativeOffset)
    {
        $relativeUrls = $this->_extractCssRelativeUrls($cssContent);
        foreach ($relativeUrls as $urlNotation => $fileUrl) {
            if (strpos($fileUrl, self::SCOPE_SEPARATOR)) {
                throw new Exception('URL offset cannot be applied to CSS content that contains scope separator.');
            }
            $fileUrlNew = $this->_canonize($relativeOffset . '/' . $fileUrl, true);
            $urlNotationNew = str_replace($fileUrl, $fileUrlNew, $urlNotation);
            $cssContent = str_replace($urlNotation, $urlNotationNew, $cssContent);
        }
        return $cssContent;
    }

    /**
     * Calculate offset between public file and public directory
     *
     * Case 1: private file to public folder - Exception;
     *  app/design/frontend/default/default/skin/default/style.css
     *  pub/skin/frontend/default/default/skin/default/style.css
     *
     * Case 2: public file to public folder - $fileOffset = '../frontend/default/default/skin/default';
     *  pub/skin/frontend/default/default/skin/default/style.css -> img/empty.gif
     *  pub/skin/_merged/hash.css -> ../frontend/default/default/skin/default/img/empty.gif
     *
     * @throws Exception
     * @param string $originalFile path to original file
     * @param string $relocationDir path to directory where content will be relocated
     * @return string
     */
    protected function _getFilesOffset($originalFile, $relocationDir)
    {
        $publicDir = Mage::getBaseDir();
        if (strpos($originalFile, $publicDir) !== 0 || strpos($relocationDir, $publicDir) !== 0) {
            throw new Exception('Offset can be calculated for public resources only.');
        }
        $offset = '';
        while ($relocationDir != $publicDir && strpos($originalFile, $relocationDir) !== 0) {
            $relocationDir = dirname($relocationDir);
            $offset .= '../';
        }
        $suffix = str_replace($relocationDir, '', dirname($originalFile));
        $offset = rtrim($offset . ltrim($suffix, '\/'), '\/');
        $offset = str_replace(DIRECTORY_SEPARATOR, '/', $offset);
        return $offset;
    }

    /**
     * Put CSS import directives to the start of CSS content
     *
     * @param string $contents
     * @return string
     */
    protected function _popCssImportsUp($contents)
    {
        $parts = preg_split('/(@import\s.+?;\s*)/', $contents, -1, PREG_SPLIT_DELIM_CAPTURE);
        $imports = array();
        $css = array();
        foreach ($parts as $part) {
            if (0 === strpos($part, '@import', 0)) {
                $imports[] = trim($part);
            } else {
                $css[] = $part;
            }
        }

        $result = implode($css);
        if ($imports) {
            $result = implode("\n", $imports). "\n"
                . "/* Import directives above popped up. */\n"
                . $result
            ;
        }
        return $result;
    }

    /**
     * Get the name of the inherited theme
     *
     * If the specified theme inherits other theme the result is the name of inherited theme.
     * If the specified theme does not inherit other theme the result is false.
     *
     * @param string $theme
     * @return bool|string
     */
    protected function _getInheritedTheme($theme)
    {
        if ($theme == self::DEFAULT_THEME_NAME) {
            return false;
        }
        return self::DEFAULT_THEME_NAME;
    }

    /**
     * Get hash key for requested file and parameters
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function _getRequestedFileKey($file, $params)
    {
        ksort($params);
        return md5(implode('_', $params) . '_' . $file);
    }

    /**
     * Get cache key for parameters
     *
     * @param array $params
     * @return string
     */
    protected function _getRequestedFileCacheKey($params)
    {
        return $params['_area'] . '/' . $params['_package'] . '/' . $params['_theme'] . '/'
            . $params['_skin'] . '/' . $params['_locale'];
    }

    /**
     * Save published file path in cache storage
     *
     * @param string $file
     * @param array $params
     * @param string $publicFile
     * @return void
     */
    protected function _setPublicFileIntoCache($file, $params, $publicFile)
    {
        $cacheKey = $this->_getRequestedFileCacheKey($params);
        $this->_loadPublicCache($cacheKey);
        $fileKey = $this->_getRequestedFileKey($file, $params);
        $this->_publicCache[$cacheKey][$fileKey] = $publicFile;
        Mage::app()->saveCache(serialize($this->_publicCache[$cacheKey]), $cacheKey, array(self::PUBLIC_CACHE_TAG));
    }

    /**
     * Load published file cache storage from cache
     *
     * @param string $cacheKey
     * @return void
     */
    protected function _loadPublicCache($cacheKey)
    {
        if (!isset($this->_publicCache[$cacheKey])) {
            if ($cache = Mage::app()->loadCache($cacheKey)) {
                $this->_publicCache[$cacheKey] = unserialize($cache);
            } else {
                $this->_publicCache[$cacheKey] = array();
            }
        }
    }
}
