<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

/**
 * Translate library
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Translate implements \Magento\TranslateInterface
{
    /**
     * Locale code
     *
     * @var string
     */
    protected $_localeCode;

    /**
     * Translator configuration array
     *
     * @var array
     */
    protected $_config;

    /**
     * Cache identifier
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Translation data
     *
     * @var []
     */
    protected $_data = [];

    /**
     * Translation data for data scope (per module)
     *
     * @var array
     */
    protected $_dataScope;

    /**
     * Locale hierarchy (empty by default)
     *
     * @var array
     */
    protected $_localeHierarchy = [];

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_viewDesign;

    /**
     * @var \Magento\Cache\FrontendInterface $cache
     */
    protected $_cache;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var \Magento\Module\ModuleList
     */
    protected $_moduleList;

    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $_modulesReader;

    /**
     * @var \Magento\BaseScopeResolverInterface
     */
    protected $_scopeResolver;

    /**
     * @var \Magento\Translate\ResourceInterface
     */
    protected $_translateResource;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_locale;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $directory;

    /**
     * @var App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\Locale\Hierarchy\Config $config
     * @param \Magento\Cache\FrontendInterface $cache
     * @param \Magento\View\FileSystem $viewFileSystem
     * @param \Magento\Module\ModuleList $moduleList
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param \Magento\BaseScopeResolverInterface $scopeResolver
     * @param \Magento\Translate\ResourceInterface $translate
     * @param \Magento\Locale\ResolverInterface $locale
     * @param \Magento\App\State $appState
     * @param \Magento\App\Filesystem $filesystem
     * @param App\RequestInterface $request
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\DesignInterface $viewDesign,
        \Magento\Locale\Hierarchy\Config $config,
        \Magento\Cache\FrontendInterface $cache,
        \Magento\View\FileSystem $viewFileSystem,
        \Magento\Module\ModuleList $moduleList,
        \Magento\Module\Dir\Reader $modulesReader,
        \Magento\BaseScopeResolverInterface $scopeResolver,
        \Magento\Translate\ResourceInterface $translate,
        \Magento\Locale\ResolverInterface $locale,
        \Magento\App\State $appState,
        \Magento\App\Filesystem $filesystem,
        \Magento\App\RequestInterface $request
    ) {
        $this->_viewDesign = $viewDesign;
        $this->_cache = $cache;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_moduleList = $moduleList;
        $this->_modulesReader = $modulesReader;
        $this->_scopeResolver = $scopeResolver;
        $this->_translateResource = $translate;
        $this->_locale = $locale;
        $this->_appState = $appState;
        $this->request = $request;
        $this->directory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->_localeHierarchy = $config->getHierarchy();
    }

    /**
     * Initialize translation data
     *
     * @param string|null $area
     * @param bool $forceReload
     * @return $this
     */
    public function loadData($area = null, $forceReload = false)
    {
        $this->setConfig(
            ['area' => isset($area) ? $area : $this->_appState->getAreaCode()]
        );

        if (!$forceReload) {
            $this->_data = $this->_loadCache();
            if ($this->_data !== false) {
                return $this;
            }
        }
        $this->_data = [];

        foreach ($this->_moduleList->getModules() as $module) {
            $this->_loadModuleTranslation($module['name']);
        }
        $this->_loadThemeTranslation($forceReload);
        $this->_loadDbTranslation($forceReload);

        if (!$forceReload) {
            $this->_saveCache();
        }

        return $this;
    }

    /**
     * Initialize configuration
     *
     * @param   array $config
     * @return  $this
     */
    protected function setConfig($config)
    {
        $this->_config = $config;
        if (!isset($this->_config['locale'])) {
            $this->_config['locale'] = $this->getLocale();
        }
        if (!isset($this->_config['scope'])) {
            $this->_config['scope'] = $this->getScope();
        }
        if (!isset($this->_config['theme'])) {
            $this->_config['theme'] = $this->_viewDesign->getDesignTheme()->getId();
        }
        return $this;
    }

    /**
     * Retrieve scope code
     *
     * @return string
     */
    protected function getScope()
    {
        $scope = ($this->getConfig('area') == 'adminhtml') ? 'admin' : null;
        return $this->_scopeResolver->getScope($scope)->getCode();
    }

    /**
     * Retrieve config value by key
     *
     * @param   string $key
     * @return  mixed
     */
    protected function getConfig($key)
    {
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }
        return null;
    }

    /**
     * Load data from module translation files
     *
     * @param string $moduleName
     * @return $this
     */
    protected function _loadModuleTranslation($moduleName)
    {
        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($requiredLocaleList as $locale) {
            $moduleFilePath = $this->_getModuleTranslationFile($moduleName, $locale);
            $this->_addData($this->_getFileData($moduleFilePath));
        }
        return $this;
    }

    /**
     * Compose the list of locales which are required to translate text entity based on given locale
     *
     * @param string $locale
     * @return string[]
     */
    protected function _composeRequiredLocaleList($locale)
    {
        $requiredLocaleList = array($locale);
        if (isset($this->_localeHierarchy[$locale])) {
            $requiredLocaleList = array_merge($this->_localeHierarchy[$locale], $requiredLocaleList);
        }
        return $requiredLocaleList;
    }

    /**
     * Adding translation data
     *
     * @param array $data
     * @param string|bool $scope
     * @param boolean $forceReload
     * @return $this
     */
    protected function _addData($data, $scope = false, $forceReload = false)
    {
        foreach ($data as $key => $value) {
            if ($key === $value) {
                continue;
            }

            $key = str_replace('""', '"', $key);
            $value  = str_replace('""', '"', $value);

            if ($scope && isset($this->_dataScope[$key]) && !$forceReload) {
                /**
                 * Checking previous value
                 */
                $scopeKey = $this->_dataScope[$key] . \Magento\View\Service::SCOPE_SEPARATOR . $key;
                if (!isset($this->_data[$scopeKey])) {
                    if (isset($this->_data[$key])) {
                        $this->_data[$scopeKey] = $this->_data[$key];
                        unset($this->_data[$key]);
                    }
                }
                $scopeKey = $scope . \Magento\View\Service::SCOPE_SEPARATOR . $key;
                $this->_data[$scopeKey] = $value;
            } else {
                $this->_data[$key] = $value;
                $this->_dataScope[$key] = $scope;
            }
        }
        return $this;
    }

    /**
     * Load current theme translation
     *
     * @param bool $forceReload
     * @return $this
     */
    protected function _loadThemeTranslation($forceReload = false)
    {
        if (!$this->_config['theme']) {
            return $this;
        }

        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($requiredLocaleList as $locale) {
            $file = $this->_getThemeTranslationFile($locale);
            $this->_addData($this->_getFileData($file), 'theme' . $this->_config['theme'], $forceReload);
        }
        return $this;
    }

    /**
     * Loading current translation from DB
     *
     * @param bool $forceReload
     * @return $this
     */
    protected function _loadDbTranslation($forceReload = false)
    {
        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($requiredLocaleList as $locale) {
            $arr = $this->_translateResource->getTranslationArray(null, $locale);
            $this->_addData($arr, $this->getConfig('scope'), $forceReload);
        }
        return $this;
    }

    /**
     * Retrieve translation file for module
     *
     * @param string $moduleName
     * @param string $locale
     * @return string
     */
    protected function _getModuleTranslationFile($moduleName, $locale)
    {
        $file = $this->_modulesReader->getModuleDir(\Magento\App\Filesystem::LOCALE_DIR, $moduleName);
        $file .= '/' . $locale . '.csv';
        return $file;
    }

    /**
     * Retrieve translation file for theme
     *
     * @param string $locale
     * @return string
     */
    protected function _getThemeTranslationFile($locale)
    {
        return $this->_viewFileSystem->getFilename(
            \Magento\App\Filesystem::LOCALE_DIR . '/' . $locale . '.csv',
            ['area' => $this->getConfig('area')]
        );
    }

    /**
     * Retrieve data from file
     *
     * @param string $file
     * @return array
     */
    protected function _getFileData($file)
    {
        $data = array();
        if ($this->directory->isExist($this->directory->getRelativePath($file))) {
            $parser = new \Magento\File\Csv();
            $parser->setDelimiter(',');
            $data = $parser->getDataPairs($file);
        }
        return $data;
    }

    /**
     * Retrieve translation data
     *
     * @return array
     */
    public function getData()
    {
        if (is_null($this->_data)) {
            return array();
        }
        return $this->_data;
    }

    /**
     * Retrieve locale
     *
     * @return string
     */
    public function getLocale()
    {
        if (null === $this->_localeCode) {
            $this->_localeCode = $this->_locale->getLocaleCode();
        }
        return $this->_localeCode;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return \Magento\TranslateInterface
     */
    public function setLocale($locale)
    {
        $this->_localeCode = $locale;
        return $this;
    }

    /**
     * Retrieve theme code
     *
     * @return string
     */
    public function getTheme()
    {
        $theme = $this->request->getParam('theme');
        if (empty($theme)) {
            return 'theme' . $this->getConfig('theme');
        }
        return 'theme' . $theme['theme_title'];
    }

    /**
     * Retrieve cache identifier
     *
     * @return string
     */
    protected function getCacheId()
    {
        if ($this->_cacheId === null) {
            $this->_cacheId = \Magento\App\Cache\Type\Translate::TYPE_IDENTIFIER;
            if (isset($this->_config['locale'])) {
                $this->_cacheId .= '_' . $this->_config['locale'];
            }
            if (isset($this->_config['area'])) {
                $this->_cacheId .= '_' . $this->_config['area'];
            }
            if (isset($this->_config['scope'])) {
                $this->_cacheId .= '_' . $this->_config['scope'];
            }
            if (isset($this->_config['theme'])) {
                $this->_cacheId .= '_' . $this->_config['theme'];
            }
        }
        return $this->_cacheId;
    }

    /**
     * Loading data cache
     *
     * @return array|bool
     */
    protected function _loadCache()
    {
        $data = $this->_cache->load($this->getCacheId());
        if ($data) {
            $data = unserialize($data);
        }
        return $data;
    }

    /**
     * Saving data cache
     *
     * @return $this
     */
    protected function _saveCache()
    {
        $this->_cache->save(serialize($this->getData()), $this->getCacheId(), array(), false);
        return $this;
    }
}
