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
 * Translate model
 *
 * @todo Remove this suppression when jira entry MAGETWO-8296 is completed.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Magento_Core_Model_Translate
{
    /**
     * CSV separator
     */
    const CSV_SEPARATOR     = ',';

    /**
     * Scope separator
     */
    const SCOPE_SEPARATOR   = '::';

    /**
     * Configuration area key
     */
    const CONFIG_KEY_AREA   = 'area';

    /**
     * Configuration locale kay
     */
    const CONFIG_KEY_LOCALE = 'locale';

    /**
     * Configuration store key
     */
    const CONFIG_KEY_STORE  = 'store';

    /**
     * Configuration theme key
     */
    const CONFIG_KEY_DESIGN_THEME   = 'theme';

    /**
     * Default translation string
     */
    const DEFAULT_STRING = 'Translate String';

    /**
     * Locale name
     *
     * @var string
     */
    protected $_locale;

    /**
     * Translation object
     *
     * @var Zend_Translate_Adapter
     */
    protected $_translate;

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
     * @var array
     */
    protected $_data = array();

    /**
     * Translation data for data scope (per module)
     *
     * @var array
     */
    protected $_dataScope;

    /**
     * Configuration flag to enable inline translations
     *
     * @var boolean
     */
    protected $_translateInline;

    /**
     * @var Magento_Core_Model_Translate_InlineInterface
     */
    protected $_inlineInterface;

    /**
     * Configuration flag to local enable inline translations
     *
     * @var boolean
     */
    protected $_canUseInline = true;

    /**
     * Locale hierarchy (empty by default)
     *
     * @var array
     */
    protected $_localeHierarchy = array();

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_viewDesign;

    /**
     * @var Magento_Core_Model_Translate_Factory
     */
    protected $_translateFactory;

    /**
     * @var Magento_Cache_FrontendInterface $cache
     */
    private $_cache;

    /**
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var Magento_Phrase_Renderer_Placeholder
     */
    protected $_placeholderRender;

    /**
     * @var Magento_Core_Model_ModuleList
     */
    protected $_moduleList;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_configObject;

    /**
     * Initialize translate model
     *
     * @param Magento_Core_Model_View_DesignInterface $viewDesign
     * @param Magento_Core_Model_Locale_Hierarchy_Loader $loader
     * @param Magento_Core_Model_Translate_Factory $translateFactory
     * @param Magento_Cache_FrontendInterface $cache
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     * @param Magento_Phrase_Renderer_Placeholder $placeholderRender
     * @param Magento_Core_Model_ModuleList $moduleList
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $viewDesign,
        Magento_Core_Model_Locale_Hierarchy_Loader $loader,
        Magento_Core_Model_Translate_Factory $translateFactory,
        Magento_Cache_FrontendInterface $cache,
        Magento_Core_Model_View_FileSystem $viewFileSystem,
        Magento_Phrase_Renderer_Placeholder $placeholderRender,
        Magento_Core_Model_ModuleList $moduleList,
        Magento_Core_Model_Config $config
    ) {
        $this->_viewDesign = $viewDesign;
        $this->_localeHierarchy = $loader->load();
        $this->_translateFactory = $translateFactory;
        $this->_cache = $cache;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_placeholderRender = $placeholderRender;
        $this->_moduleList = $moduleList;
        $this->_configObject = $config;
    }

    /**
     * Initialization translation data
     *
     * @param string $area
     * @param Magento_Object $initParams
     * @param bool $forceReload
     * @return Magento_Core_Model_Translate
     */
    public function init($area, $initParams = null, $forceReload = false)
    {
        $this->setConfig(array(self::CONFIG_KEY_AREA => $area));

        $this->_translateInline = $this->getInlineObject($initParams)->isAllowed(
            $area == Magento_Backend_Helper_Data::BACKEND_AREA_CODE ? 'admin' : null);

        if (!$forceReload) {
            $this->_data = $this->_loadCache();
            if ($this->_data !== false) {
                return $this;
            }
        }

        $this->_data = array();

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
     * @return  Magento_Core_Model_Translate
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        if (!isset($this->_config[self::CONFIG_KEY_LOCALE])) {
            $this->_config[self::CONFIG_KEY_LOCALE] = $this->getLocale();
        }
        if (!isset($this->_config[self::CONFIG_KEY_STORE])) {
            $this->_config[self::CONFIG_KEY_STORE] = Mage::app()->getStore()->getId();
        }
        if (!isset($this->_config[self::CONFIG_KEY_DESIGN_THEME])) {
            $this->_config[self::CONFIG_KEY_DESIGN_THEME] = $this->_viewDesign->getDesignTheme()->getId();
        }
        return $this;
    }

    /**
     * Retrieve config value by key
     *
     * @param   string $key
     * @return  mixed
     */
    public function getConfig($key)
    {
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }
        return null;
    }

    /**
     * Determine if translation is enabled and allowed.
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        /** @todo see jira entry MAGETWO-8296 */
        return $this->getInlineObject()->isAllowed($store);
    }

    /**
     * Parse and save edited translate
     *
     * @param array $translate
     * @return Magento_Core_Model_Translate_InlineInterface
     */
    public function processAjaxPost($translate)
    {
        /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
        $cacheTypeList = $this->_translateFactory->create('Magento_Core_Model_Cache_TypeListInterface');
        $cacheTypeList->invalidate(Magento_Core_Model_Cache_Type_Translate::TYPE_IDENTIFIER);
        /** @var $parser Magento_Core_Model_Translate_InlineParser */
        $parser = $this->_translateFactory->create('Magento_Core_Model_Translate_InlineParser');
        $parser->processAjaxPost($translate, $this->getInlineObject());
    }

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     * @return Magento_Core_Model_Translate_InlineInterface
     */
    public function processResponseBody(&$body,
        $isJson = Magento_Core_Model_Translate_InlineParser::JSON_FLAG_DEFAULT_STATE
    ) {
        return $this->getInlineObject()->processResponseBody($body, $isJson);
    }

    /**
     * Load data from module translation files
     *
     * @param $moduleName string
     * @return $this
     */
    protected function _loadModuleTranslation($moduleName)
    {
        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($requiredLocaleList as $locale) {
            $moduleFilePath = $this->_getModuleFilePath($moduleName, $locale);
            $this->_addData($this->_getFileData($moduleFilePath));
        }
        return $this;
    }

    /**
     * Compose the list of locales which are required to translate text entity based on given locale
     *
     * @param string $locale
     * @return array
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
     * @return Magento_Core_Model_Translate
     */
    protected function _addData($data, $scope = false, $forceReload = false)
    {
        foreach ($data as $key => $value) {
            if ($key === $value) {
                continue;
            }
            $key    = $this->_prepareDataString($key);
            $value  = $this->_prepareDataString($value);
            if ($scope && isset($this->_dataScope[$key]) && !$forceReload ) {
                /**
                 * Checking previous value
                 */
                $scopeKey = $this->_dataScope[$key] . self::SCOPE_SEPARATOR . $key;
                if (!isset($this->_data[$scopeKey])) {
                    if (isset($this->_data[$key])) {
                        $this->_data[$scopeKey] = $this->_data[$key];
                        unset($this->_data[$key]);
                    }
                }
                $scopeKey = $scope . self::SCOPE_SEPARATOR . $key;
                $this->_data[$scopeKey] = $value;
            } else {
                $this->_data[$key] = $value;
                $this->_dataScope[$key]= $scope;
            }
        }
        return $this;
    }

    /**
     * Prepare data string
     *
     * @param string $string
     * @return string
     */
    protected function _prepareDataString($string)
    {
        return str_replace('""', '"', $string);
    }

    /**
     * Load current theme translation
     *
     * @param boolean $forceReload
     * @return Magento_Core_Model_Translate
     */
    protected function _loadThemeTranslation($forceReload = false)
    {
        if (!$this->_config[self::CONFIG_KEY_DESIGN_THEME]) {
            return $this;
        }

        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($requiredLocaleList as $locale) {
            $file = $this->_viewFileSystem->getLocaleFileName($locale . '.csv');
            $this->_addData(
                $this->_getFileData($file),
                self::CONFIG_KEY_DESIGN_THEME . $this->_config[self::CONFIG_KEY_DESIGN_THEME],
                $forceReload
            );
        }
        return $this;
    }

    /**
     * Loading current store translation from DB
     *
     * @param boolean $forceReload
     * @return Magento_Core_Model_Translate
     */
    protected function _loadDbTranslation($forceReload = false)
    {
        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($requiredLocaleList as $locale) {
            $arr = $this->getResource()->getTranslationArray(null, $locale);
            $this->_addData($arr, $this->getConfig(self::CONFIG_KEY_STORE), $forceReload);
        }
        return $this;
    }

    /**
     * Retrieve translation file for module
     *
     * @param $moduleName string
     * @param $locale string
     * @return string
     */
    protected function _getModuleFilePath($moduleName, $locale)
    {
        $file = $this->_configObject->getModuleDir('i18n', $moduleName);
        $file .= DS . $locale . '.csv';
        return $file;
    }

    /**
     * Retrieve data from file
     *
     * @param   string $file
     * @return  array
     */
    protected function _getFileData($file)
    {
        $data = array();
        if (file_exists($file)) {
            $parser = new Magento_File_Csv();
            $parser->setDelimiter(self::CSV_SEPARATOR);
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
        if (null === $this->_locale) {
            $this->_locale = Mage::app()->getLocale()->getLocaleCode();
        }
        return $this->_locale;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return Magento_Core_Model_Translate
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;
    }

    /**
     * Retrieve DB resource model
     *
     * @return Magento_Core_Model_Resource_Translate
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('Magento_Core_Model_Resource_Translate');
    }

    /**
     * Retrieve translation object
     *
     * @return Zend_Translate_Adapter
     */
    public function getTranslate()
    {
        if (null === $this->_translate) {
            $this->_translate = new Zend_Translate('array', $this->getData(), $this->getLocale());
        }
        return $this->_translate;
    }

    /**
     * Translate
     *
     * @param array $args
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function translate($args)
    {
        $text = array_shift($args);

        if ($this->_isEmptyTranslateArg($text)) {
            return '';
        }

        if (!empty($_REQUEST['theme'])) {
            $module = self::CONFIG_KEY_DESIGN_THEME . $_REQUEST['theme'];
        } else {
            $module = self::CONFIG_KEY_DESIGN_THEME . $this->_config[self::CONFIG_KEY_DESIGN_THEME];
        }
        $code = $module . self::SCOPE_SEPARATOR . $text;
        $translated = $this->_getTranslatedString($text, $code);
        $result = $this->_placeholderRender->render($translated, $args);

        if ($this->_translateInline && $this->getTranslateInline()) {
            if (strpos($result, '{{{') === false
                || strpos($result, '}}}') === false
                || strpos($result, '}}{{') === false
            ) {
                $result = '{{{' . $result . '}}{{' . $translated . '}}{{' . $text . '}}{{' . $module . '}}}';
            }
        }
        return $result;
    }

    /**
     * Check is empty translate argument
     *
     * @param mixed $text
     * @return bool
     */
    protected function _isEmptyTranslateArg($text)
    {
        if (is_object($text) && is_callable(array($text, 'getText'))) {
            $text = $text->getText();
        }
        return empty($text);
    }

    /**
     * Set Translate inline mode
     *
     * @param bool $flag
     * @return Magento_Core_Model_Translate
     */
    public function setTranslateInline($flag = false)
    {
        $this->_canUseInline = $flag;
        return $this;
    }

    /**
     * Retrieve active translate mode
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getTranslateInline()
    {
        return $this->_canUseInline;
    }

    /**
     * Retrieve cache identifier
     *
     * @return string
     */
    public function getCacheId()
    {
        if (is_null($this->_cacheId)) {
            $this->_cacheId = Magento_Core_Model_Cache_Type_Translate::TYPE_IDENTIFIER;
            if (isset($this->_config[self::CONFIG_KEY_LOCALE])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_LOCALE];
            }
            if (isset($this->_config[self::CONFIG_KEY_AREA])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_AREA];
            }
            if (isset($this->_config[self::CONFIG_KEY_STORE])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_STORE];
            }
            if (isset($this->_config[self::CONFIG_KEY_DESIGN_THEME])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_DESIGN_THEME];
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
     * @return Magento_Core_Model_Translate
     */
    protected function _saveCache()
    {
        $this->_cache->save(serialize($this->getData()), $this->getCacheId(), array(), false);
        return $this;
    }

    /**
     * Return translated string from text.
     *
     * @param string $text
     * @param string $code
     * @return string
     */
    protected function _getTranslatedString($text, $code)
    {
        if (array_key_exists($code, $this->getData())) {
            $translated = $this->_data[$code];
        } elseif (array_key_exists($text, $this->getData())) {
            $translated = $this->_data[$text];
        } else {
            $translated = $text;
        }
        return $translated;
    }

    /**
     * Returns the translate interface object.
     *
     * @param Magento_Object $initParams
     * @return Magento_Core_Model_Translate_InlineInterface
     */
    private function getInlineObject($initParams = null)
    {
        if (null === $this->_inlineInterface) {
            if ($initParams === null) {
                $this->_inlineInterface = $this->_translateFactory->create();
            } else {
                $this->_inlineInterface = $this->_translateFactory
                    ->create($initParams->getParams(), $initParams->getInlineType());
            }
        }
        return $this->_inlineInterface;
    }
}
