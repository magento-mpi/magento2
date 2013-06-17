<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_View_FileSystem implements Mage_Core_Model_View_FileSystemInterface
{
    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dirs;

    /**
     * List of view configuration objects per theme
     *
     * @var array
     */
    protected $_viewConfigs = array();

    /**
     * Model, used to resolve the file paths
     *
     * @var Mage_Core_Model_Design_FileResolution_StrategyPool
     */
    protected $_resolutionPool = null;

    /**
     * Module configuration reader
     *
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_App_State
     */
    protected $_appState;

    /**
     * Helper to process css content
     *
     * @var Mage_Core_Helper_Css
     */
    protected $_cssHelper;

    //new
    /**
     * @var Mage_Core_Model_View_Publisher
     */
    private $_publisher;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    private $_design;


    /**
     * View files system model
     *
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Design_FileResolution_StrategyPool $resolutionPool
     * @param Mage_Core_Model_App_State $appState
     * @param Mage_Core_Helper_Css $cssHelper
     * @param Mage_Core_Model_View_Publisher $publisher
     */
    public function __construct(
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Design_FileResolution_StrategyPool $resolutionPool,
        Mage_Core_Model_App_State $appState,
        Mage_Core_Helper_Css $cssHelper,
        Mage_Core_Model_View_Publisher $publisher
    ) {
        $this->_dirs = $dirs;
        $this->_moduleReader = $moduleReader;
        $this->_filesystem = $filesystem;
        $this->_resolutionPool = $resolutionPool;
        $this->_appState = $appState;
        $this->_cssHelper = $cssHelper;
        $this->_publisher = $publisher;
    }

    public function setDesign(Mage_Core_Model_Design_Package $design)
    {
        $this->_design = $design;
    }




    // file system

    /**
     * Get existing file name with fallback to default
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params = array())
    {
        $file = $this->_extractScope($file, $params);
        $this->_updateParamDefaults($params);
        return $this->_resolutionPool->getFileStrategy(!empty($params['skipProxy']))
            ->getFile($params['area'], $params['themeModel'], $file, $params['module']);
    }

    /**
     * Get a locale file
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getLocaleFileName($file, array $params = array())
    {
        $this->_updateParamDefaults($params);
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        return $this->_resolutionPool->getLocaleStrategy($skipProxy)->getLocaleFile($params['area'],
            $params['themeModel'], $params['locale'], $file);
    }

    /**
     * Find a view file using fallback mechanism
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFile($file, array $params = array())
    {
        $file = $this->_extractScope($file, $params);
        $this->_updateParamDefaults($params);
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        return $this->_resolutionPool->getViewStrategy($skipProxy)->getViewFile($params['area'],
            $params['themeModel'], $params['locale'], $file, $params['module']);
    }

    /**
     * Render view config object for current package and theme
     *
     * @param array $params
     * @return Magento_Config_View
     */
    public function getViewConfig(array $params = array())
    {
        $this->_updateParamDefaults($params);
        /** @var $currentTheme Mage_Core_Model_Theme */
        $currentTheme = $params['themeModel'];
        $key = $currentTheme->getId();
        if (isset($this->_viewConfigs[$key])) {
            return $this->_viewConfigs[$key];
        }

        $configFiles = $this->_moduleReader->getModuleConfigurationFiles(Mage_Core_Model_Theme::FILENAME_VIEW_CONFIG);
        $themeConfigFile = $currentTheme->getCustomViewConfigPath();
        if (empty($themeConfigFile) || !$this->_filesystem->has($themeConfigFile)) {
            $themeConfigFile = $this->getFilename(Mage_Core_Model_Theme::FILENAME_VIEW_CONFIG, $params);
        }
        if ($themeConfigFile && $this->_filesystem->has($themeConfigFile)) {
            $configFiles[] = $themeConfigFile;
        }
        $config = new Magento_Config_View($configFiles);

        $this->_viewConfigs[$key] = $config;
        return $config;
    }

    /**
     * Identify file scope if it defined in file name and override _module parameter in $params array
     *
     * @param string $file
     * @param array &$params
     * @return string
     * @throws Magento_Exception
     */
    protected function _extractScope($file, array &$params)
    {
        if (preg_match('/\.\//', str_replace('\\', '/', $file))) {
            throw new Magento_Exception("File name '{$file}' is forbidden for security reasons.");
        }
        if (false !== strpos($file, Mage_Core_Model_Design_Package::SCOPE_SEPARATOR)) {
            $file = explode(Mage_Core_Model_Design_Package::SCOPE_SEPARATOR, $file);
            if (empty($file[0])) {
                throw new Magento_Exception('Scope separator "::" cannot be used without scope identifier.');
            }
            $params['module'] = $file[0];
            $file = $file[1];
        }
        return $file;
    }

    /**
     * Update required parameters with default values if custom not specified
     *
     * @param array $params
     * @return Mage_Core_Model_Design_Package
     */
    protected function _updateParamDefaults(array &$params)
    {
        if (empty($params['area'])) {
            $params['area'] = $this->_design->getArea();
        }

        if (!empty($params['themeId'])) {
            $params['themeModel'] = $this->_design->_getLoadDesignTheme($params['themeId'], $params['area']);
        } elseif (!empty($params['package']) && isset($params['theme'])) {
            $themePath = $params['package'] . '/' . $params['theme'];
            $params['themeModel'] = $this->_design->_getLoadDesignTheme($themePath, $params['area']);
        } elseif (empty($params['themeModel']) && $params['area'] !== $this->_design->getArea()) {
            $params['themeModel'] = $this->_design->_getLoadDesignTheme(
                $this->_design->getConfigurationDesignTheme($params['area']),
                $params['area']
            );
        } elseif (empty($params['themeModel'])) {
            $params['themeModel'] = $this->_design->getDesignTheme();
        }

        if (!array_key_exists('module', $params)) {
            $params['module'] = false;
        }
        if (empty($params['locale'])) {
            $params['locale'] = Mage::app()->getLocale()->getLocaleCode();
        }
        return $this;
    }

    protected function _updateParamDefaultsOld(array &$params)
    {
        if (empty($params['area'])) {
            $params['area'] = $this->getArea();
        }

        if (!empty($params['themeId'])) {
            $params['themeModel'] = $this->_getLoadDesignTheme($params['themeId'], $params['area']);
        } elseif (!empty($params['package']) && isset($params['theme'])) {
            $themePath = $params['package'] . '/' . $params['theme'];
            $params['themeModel'] = $this->_getLoadDesignTheme($themePath, $params['area']);
        } elseif (empty($params['themeModel']) && $params['area'] !== $this->getArea()) {
            $params['themeModel'] = $this->_getLoadDesignTheme(
                $this->getConfigurationDesignTheme($params['area']),
                $params['area']
            );
        } elseif (empty($params['themeModel'])) {
            $params['themeModel'] = $this->getDesignTheme();
        }

        if (!array_key_exists('module', $params)) {
            $params['module'] = false;
        }
        if (empty($params['locale'])) {
            $params['locale'] = Mage::app()->getLocale()->getLocaleCode();
        }
        return $this;
    }


    // publisher

    /**
     * Get url to file base on theme file identifier.
     * Publishes file there, if needed.
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($file, array $params = array())
    {
        $isSecure = isset($params['_secure']) ? (bool) $params['_secure'] : null;
        unset($params['_secure']);

        $publicFile = $this->getViewFilePublicPath($file, $params);
        $url = $this->getPublicFileUrl($publicFile, $isSecure);

        return $url;
    }

    /**
     * Get url to public file
     *
     * @param string $file
     * @param bool|null $isSecure
     * @return string
     * @throws Magento_Exception
     */
    public function getPublicFileUrl($file, $isSecure = null)
    {
        foreach (array(
                Mage_Core_Model_Store::URL_TYPE_LIB     => Mage_Core_Model_Dir::PUB_LIB,
                Mage_Core_Model_Store::URL_TYPE_MEDIA   => Mage_Core_Model_Dir::MEDIA,
                Mage_Core_Model_Store::URL_TYPE_STATIC  => Mage_Core_Model_Dir::STATIC_VIEW,
                Mage_Core_Model_Store::URL_TYPE_CACHE   => Mage_Core_Model_Dir::PUB_VIEW_CACHE,
            ) as $urlType => $dirType
        ) {
            $dir = Mage::getBaseDir($dirType);
            if (strpos($file, $dir) === 0) {
                $relativePath = ltrim(substr($file, strlen($dir)), '\\/');
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $url = Mage::getBaseUrl($urlType, $isSecure) . $relativePath;
                if ($this->_isStaticFilesSigned() && $this->_isViewFileOperationAllowed()) {
                    $fileMTime = $this->_filesystem->getMTime($file);
                    $url .= '?' . $fileMTime;
                }
                return $url;
            }
        }
        throw new Magento_Exception(
            "Cannot build URL for the file '$file' because it does not reside in a public directory."
        );
    }

    /**
     * Publish relative $fileUrl based on information about parent file path and name.
     *
     * The method is public only because PHP 5.3 does not permit usage of protected methods inside the closures,
     * even if a closure is created in the same class. The method is not intended to be used by a client of this class.
     * If you ever need to call this method externally, then ensure you have a good reason for it. As such the method
     * would need to be added to the class's interface and proxy.
     *
     * @param string $fileUrl URL to the file that was extracted from $parentFilePath
     * @param string $parentFilePath path to the file
     * @param string $parentFileName original file name identifier that was requested for processing
     * @param array $params theme/module parameters array
     * @return string
     */
    public function publishRelatedViewFile($fileUrl, $parentFilePath, $parentFileName, $params)
    {
        if (strpos($fileUrl, Mage_Core_Model_Design_Package::SCOPE_SEPARATOR)) {
            $relativeThemeFile = $this->_extractScope($fileUrl, $params);
        } else {
            /* Check if module file overridden on theme level based on _module property and file path */
            if ($params['module'] && strpos($parentFilePath, Mage::getBaseDir(Mage_Core_Model_Dir::THEMES)) === 0) {
                /* Add module directory to relative URL */
                $relativeThemeFile = dirname($params['module'] . '/' . $parentFileName)
                    . '/' . $fileUrl;
                $relativeThemeFile = $this->_filesystem->normalizePath($relativeThemeFile, true);
                if (strpos($relativeThemeFile, $params['module']) === 0) {
                    $relativeThemeFile = ltrim(str_replace($params['module'], '', $relativeThemeFile), '/');
                } else {
                    $params['module'] = false;
                }
            } else {
                $relativeThemeFile = $this->_filesystem->normalizePath(dirname($parentFileName) . '/' . $fileUrl, true);
            }
        }
        return $this->_publishViewFile($relativeThemeFile, $params);
    }

    /**
     * Build a relative path to a static view file, if published with duplication.
     *
     * Just concatenates all context arguments.
     * Note: despite $locale is specified, it is currently ignored.
     *
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public static function getPublishedViewFileRelPath($area, $themePath, $locale, $file, $module = null)
    {
        return $area . DIRECTORY_SEPARATOR . $themePath . DIRECTORY_SEPARATOR
            . ($module ? $module . DIRECTORY_SEPARATOR : '') . $file;
    }

    /**
     * Publish file (if needed) and return its public path
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getViewFilePublicPath($file, array $params = array())
    {
        $this->_updateParamDefaults($params);
        $file = $this->_extractScope($file, $params);

        if ($this->_isViewFileOperationAllowed()) {
            $result = $this->_publishViewFile($file, $params);
        } else {
            /** @var $themeModel Mage_Core_Model_Theme */
            $themeModel = $params['themeModel'];
            $themePath = $themeModel->getThemePath();
            while (empty($themePath) && $themeModel) {
                $themePath = $themeModel->getThemePath();
                $themeModel = $themeModel->getParentTheme();
            }
            $subPath = self::getPublishedViewFileRelPath($params['area'], $themePath, $params['locale'], $file,
                $params['module']);
            $result = $this->getPublicDir() . DIRECTORY_SEPARATOR . $subPath;
        }
        return $result;
    }

    /**
     * Return directory for theme files publication
     *
     * @return string
     */
    public function getPublicDir()
    {
        return Mage::getBaseDir(Mage_Core_Model_Dir::STATIC_VIEW);
    }

    /**
     * Check, if requested theme file has public access, and move it to public folder, if the file has no public access
     *
     * @param  string $themeFile
     * @param  array $params
     * @return string
     * @throws Magento_Exception
     */
    protected function _publishViewFile($themeFile, $params)
    {
        if (!$this->_isViewFileOperationAllowed()) {
            throw new Magento_Exception('Filesystem operations are not permitted for view files');
        }

        $sourcePath = $this->getViewFile($themeFile, $params);

        if (!$this->_filesystem->has($sourcePath)) {
            throw new Magento_Exception("Unable to locate theme file '{$sourcePath}'.");
        }
        if (!$this->_needToProcessFile($sourcePath)) {
            return $sourcePath;
        }

        $allowPublication = (string)Mage::getConfig()->getNode(Mage_Core_Model_Design_Package::XML_PATH_ALLOW_DUPLICATION);
        if ($allowPublication || $this->_getExtension($themeFile) == Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS) {
            $targetPath = $this->_buildPublicViewRedundantFilename($themeFile, $params);
        } else {
            $targetPath = $this->_buildPublicViewSufficientFilename($sourcePath, $params);
        }
        $targetPath = $this->_buildPublicViewFilename($targetPath);

        /* Validate whether file needs to be published */
        if ($this->_getExtension($themeFile) == Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS) {
            $cssContent = $this->_getPublicCssContent($sourcePath, $targetPath, $themeFile, $params);
        }

        $fileMTime = $this->_filesystem->getMTime($sourcePath);
        if (!$this->_filesystem->has($targetPath) || $fileMTime != $this->_filesystem->getMTime($targetPath)) {
            $publicDir = dirname($targetPath);
            if (!$this->_filesystem->isDirectory($publicDir)) {
                $this->_filesystem->createDirectory($publicDir, 0777);
            }

            if (isset($cssContent)) {
                $this->_filesystem->write($targetPath, $cssContent);
                $this->_filesystem->touch($targetPath, $fileMTime);
            } elseif ($this->_filesystem->isFile($sourcePath)) {
                $this->_filesystem->copy($sourcePath, $targetPath);
                $this->_filesystem->touch($targetPath, $fileMTime);
            } elseif (!$this->_filesystem->isDirectory($targetPath)) {
                $this->_filesystem->createDirectory($targetPath, 0777);
            }
        }

        $this->_notifyViewFileLocationChanged($targetPath, $themeFile, $params);
        return $targetPath;
    }

    /**
     * Verify whether we should work with files
     *
     * @return bool
     */
    protected function _isViewFileOperationAllowed()
    {
        return $this->_getAppMode() != Mage_Core_Model_App_State::MODE_PRODUCTION;
    }

    /**
     * Build public filename for a theme file that always includes area/package/theme/locate parameters
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    protected function _buildPublicViewRedundantFilename($file, array $params)
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $params['themeModel'];
        if ($theme->getThemePath()) {
            $designPath = str_replace('/', DS, $theme->getThemePath());
        } elseif ($theme->getId()) {
            $designPath = Mage_Core_Model_Design_Package::PUBLIC_THEME_DIR . $theme->getId();
        } else {
            $designPath = Mage_Core_Model_Design_Package::PUBLIC_VIEW_DIR;
        }

        $publicFile = $params['area'] . DS . $designPath . DS . $params['locale'] .
            ($params['module'] ? DS . $params['module'] : '') . DS . $file;

        return $publicFile;
    }

    /**
     * Build public filename for a view file that sufficiently depends on the passed parameters
     *
     * @param string $filename
     * @param array $params
     * @return string
     */
    protected function _buildPublicViewSufficientFilename($filename, array $params)
    {
        $designDir = Mage::getBaseDir(Mage_Core_Model_Dir::THEMES) . DS;
        if (0 === strpos($filename, $designDir)) {
            // theme file
            $publicFile = substr($filename, strlen($designDir));
        } else {
            // modular file
            $module = $params['module'];
            $moduleDir = Mage::getModuleDir('theme', $module) . DS;
            $publicFile = substr($filename, strlen($moduleDir));
            $publicFile = Mage_Core_Model_Design_Package::PUBLIC_MODULE_DIR . DS . $module . DS . $publicFile;
        }
        return $publicFile;
    }

    /**
     * Retrieve processed CSS file content that contains URLs relative to the specified public directory
     *
     * @param string $sourcePath Absolute path to the current location of CSS file
     * @param string $publicPath Absolute path to location of the CSS file, where it will be published
     * @param string $fileName File name used for reference
     * @param array $params Design parameters
     * @return string
     */
    protected function _getPublicCssContent($sourcePath, $publicPath, $fileName, $params)
    {
        $content = $this->_filesystem->read($sourcePath);

        $package = $this;
        $callback = function ($relativeUrl, $originalPath) use ($package, $fileName, $params) {
            $relatedFilePathPublic = $package->publishRelatedViewFile($relativeUrl, $originalPath, $fileName, $params);
            return $relatedFilePathPublic;
        };
        try {
            $content = $this->_cssHelper->replaceCssRelativeUrls($content, $sourcePath, $publicPath, $callback);
        } catch (Magento_Exception $e) {
            Mage::logException($e);
        }
        return $content;
    }

    /**
     * Determine whether a file needs to be published.
     * Js files are never processed. All other files must be processed either if they are not published already,
     * or if they are css-files and we're working in developer mode.
     *
     * @param string $filePath
     * @return bool
     */
    protected function _needToProcessFile($filePath)
    {
        $jsPath = Mage::getBaseDir(Mage_Core_Model_Dir::PUB_LIB) . DS;
        if (strncmp($filePath, $jsPath, strlen($jsPath)) === 0) {
            return false;
        }

        $protectedExtensions = array(Mage_Core_Model_Design_Package::CONTENT_TYPE_PHP, Mage_Core_Model_Design_Package::CONTENT_TYPE_PHTML, Mage_Core_Model_Design_Package::CONTENT_TYPE_XML);
        if (in_array($this->_getExtension($filePath), $protectedExtensions)) {
            return false;
        }

        $themePath = $this->getPublicDir() . DS;
        if (strncmp($filePath, $themePath, strlen($themePath)) !== 0) {
            return true;
        }

        return ($this->_getAppMode() == Mage_Core_Model_App_State::MODE_DEVELOPER)
            && $this->_getExtension($filePath) == Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS;
    }

    /**
     * Notify that view file resolved path was changed (i.e. it was published to a public directory)
     *
     * @param $targetPath
     * @param $themeFile
     * @param $params
     * @return Mage_Core_Model_Design_Package
     */
    protected function _notifyViewFileLocationChanged($targetPath, $themeFile, $params)
    {
        $skipProxy = isset($params['skipProxy']) && $params['skipProxy'];
        $strategy = $this->_resolutionPool->getViewStrategy($skipProxy);
        if ($strategy instanceof Mage_Core_Model_Design_FileResolution_Strategy_View_NotifiableInterface) {
            /** @var $strategy Mage_Core_Model_Design_FileResolution_Strategy_View_NotifiableInterface  */
            $themeFile = $this->_extractScope($themeFile, $params);
            $this->_updateParamDefaults($params);
            $strategy->setViewFilePathToMap($params['area'], $params['themeModel'], $params['locale'],
                $params['module'], $themeFile, $targetPath);
        }

        return $this;
    }

    /**
     * Return whether developer mode is turned on
     *
     * @return bool
     */
    protected function _getAppMode()
    {
        return $this->_appState->getMode();
    }

    /**
     * Check if static files have to be signed
     *
     * @return bool
     */
    protected function _isStaticFilesSigned()
    {
        return (bool)Mage::getStoreConfig(Mage_Core_Model_Design_Package::XML_PATH_STATIC_FILE_SIGNATURE);
    }

    /**
     * Get file extension by file path
     *
     * @param string $filePath
     * @return string
     */
    protected function _getExtension($filePath)
    {
        $dotPosition = strrpos($filePath, '.');
        return strtolower(substr($filePath, $dotPosition + 1));
    }

    /**
     * Build path to file located in public folder
     *
     * @param string $file
     * @return string
     */
    protected function _buildPublicViewFilename($file)
    {
        return $this->getPublicDir() . DS . $file;
    }
}
