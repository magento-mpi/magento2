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
 * Translate model
 *
 * @todo Remove this suppression when jira entry MAGETWO-8296 is completed.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Core_Model_Translate
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
     * Cache tag
     */
    const CACHE_TAG         = Mage_Core_Model_Cache_Type_Translate::CACHE_TAG;

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
     * @var bool
     */
    protected $_useCache = true;

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
     * @var Mage_Core_Model_Translate_TranslateInterface
     */
    protected $_translateObject;

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
     *
     * @var
     */
    protected $_translateConfig;
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize translate model
     *
     * @param Mage_Core_Model_Locale_Hierarchy_Loader $loader
     * @param Mage_Core_Model_Translate_Config $translateConfig
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Core_Model_Locale_Hierarchy_Loader $loader,
        Mage_Core_Model_Translate_Config $translateConfig,
        Magento_ObjectManager $objectManager
    ) {
        $this->_translateConfig = $translateConfig;
        $this->_localeHierarchy = $loader->load();
        $this->_objectManager = $objectManager;
    }

    /**
     * Initialization translation data
     *
     * @return Mage_Core_Model_Translate
     */
    public function init()
    {
        $area = $this->_translateConfig->getArea();
        $forceReload = $this->_translateConfig->getForceReload();

        $this->setConfig(array(self::CONFIG_KEY_AREA => $area));

        /** @var $translateFactory Mage_Core_Model_Translate_Factory */
        $translateFactory = $this->_objectManager->get('Mage_Core_Model_Translate_Factory');
        $this->_translateObject = $translateFactory->createFromConfig($this->_translateConfig);

        $this->_translateInline = $this->_translateObject->isAllowed(
            $area == Mage_Backend_Helper_Data::BACKEND_AREA_CODE ? 'admin' : null);

        if (!$forceReload) {
            if ($this->_canUseCache()) {
                $this->_data = $this->_loadCache();
                if ($this->_data !== false) {
                    return $this;
                }
            }
            Mage::app()->removeCache($this->getCacheId());
        }

        $this->_data = array();

        foreach ($this->getModulesConfig() as $moduleName => $info) {
            $info = $info->asArray();
            $this->_loadModuleTranslation($moduleName, $info['files'], $forceReload);
        }

        $this->_loadThemeTranslation($forceReload);
        $this->_loadDbTranslation($forceReload);

        if (!$forceReload && $this->_canUseCache()) {
            $this->_saveCache();
        }

        return $this;
    }

    /**
     * Retrieve modules configuration by translation
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getModulesConfig()
    {
        if (!Mage::getConfig()->getNode($this->getConfig(self::CONFIG_KEY_AREA) . '/translate/modules')) {
            return array();
        }

        $config = Mage::getConfig()->getNode($this->getConfig(self::CONFIG_KEY_AREA)
            . '/translate/modules')->children();
        if (!$config) {
            return array();
        }
        return $config;
    }

    /**
     * Initialize configuration
     *
     * @param   array $config
     * @return  Mage_Core_Model_Translate
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
            /** @var $designPackage Mage_Core_Model_Design_Package */
            $designPackage = $this->_objectManager->get('Mage_Core_Model_Design_Package');
            $this->_config[self::CONFIG_KEY_DESIGN_THEME] = $designPackage->getDesignTheme()->getId();
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
        return $this->_translateObject->isAllowed($store);
    }

    /**
     * Parse and save edited translate
     *
     * @param array $translate
     * @return Mage_Core_Model_Translate_TranslateInterface
     */
    public function processAjaxPost($translate)
    {
        return $this->_translateObject->processAjaxPost($translate);
    }

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     * @return Mage_Core_Model_Translate_TranslateInterface
     */
    public function processResponseBody(&$body,
        $isJson = Mage_Core_Model_Translate_InlineAbstract::JSON_FLAG_DEFAULT_STATE
    ) {
        return $this->_translateObject->processResponseBody($body, $isJson);
    }

    /**
     * Returns the translate config for this translate instance.
     *
     * @return Mage_Core_Model_Translate_Config
     */
    public function getTranslateConfig()
    {
        return $this->_translateConfig;
    }

    /**
     * Load data from module translation files
     *
     * @param string $moduleName
     * @param array $files
     * @param boolean $forceReload
     * @return Mage_Core_Model_Translate
     */
    protected function _loadModuleTranslation($moduleName, $files, $forceReload = false)
    {
        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($files as $file) {
            foreach ($requiredLocaleList as $locale) {
                $moduleFilePath = $this->_getModuleFilePath($moduleName, $file, $locale);
                $this->_addData($this->_getFileData($moduleFilePath), $moduleName, $forceReload);
            }
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
     * @param string $scope
     * @param boolean $forceReload
     * @return Mage_Core_Model_Translate
     */
    protected function _addData($data, $scope, $forceReload = false)
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
     * @return Mage_Core_Model_Translate
     */
    protected function _loadThemeTranslation($forceReload = false)
    {
        if (!$this->_config[self::CONFIG_KEY_DESIGN_THEME]) {
            return $this;
        }

        $requiredLocaleList = $this->_composeRequiredLocaleList($this->getLocale());
        foreach ($requiredLocaleList as $locale) {
            /** @var $designPackage Mage_Core_Model_Design_Package */
            $designPackage = $this->_objectManager->get('Mage_Core_Model_Design_Package');
            $file = $designPackage->getLocaleFileName('translate.csv', array('locale' => $locale));
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
     * @return Mage_Core_Model_Translate
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
     * @param string $module
     * @param string $fileName
     * @param string $locale
     * @return string
     */
    protected function _getModuleFilePath($module, $fileName, $locale)
    {
        $file = Mage::getModuleDir('locale', $module);
        $file .= DS . $locale . DS . $fileName;
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
            $parser = new Varien_File_Csv();
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
        if (is_null($this->_locale)) {
            $this->_locale = Mage::app()->getLocale()->getLocaleCode();
        }
        return $this->_locale;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return Mage_Core_Model_Translate
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;
    }

    /**
     * Retrieve DB resource model
     *
     * @return Mage_Core_Model_Resource_Translate
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('Mage_Core_Model_Resource_Translate');
    }

    /**
     * Retrieve translation object
     *
     * @return Zend_Translate_Adapter
     */
    public function getTranslate()
    {
        if (is_null($this->_translate)) {
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

        if ($text instanceof Mage_Core_Model_Translate_Expr) {
            $code = $text->getCode(self::SCOPE_SEPARATOR);
            $module = $text->getModule();
            $text = $text->getText();
            $translated = $this->_getTranslatedString($text, $code);
        } else {
            if (!empty($_REQUEST['theme'])) {
                $module = self::CONFIG_KEY_DESIGN_THEME . $_REQUEST['theme'];
            } else {
                $module = self::CONFIG_KEY_DESIGN_THEME . $this->_config[self::CONFIG_KEY_DESIGN_THEME];
            }
            $code = $module . self::SCOPE_SEPARATOR . $text;
            $translated = $this->_getTranslatedString($text, $code);
        }

        $result = vsprintf($translated, $args);
        if ($result === false) {
            $result = $translated;
        }

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
     * @return Mage_Core_Model_Translate
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
            $this->_cacheId = Mage_Core_Model_Cache_Type_Translate::TYPE_IDENTIFIER;
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
     * @return  array|false
     */
    protected function _loadCache()
    {
        if (!$this->_canUseCache()) {
            return false;
        }
        $data = Mage::app()->loadCache($this->getCacheId());
        $data = unserialize($data);
        return $data;
    }

    /**
     * Saving data cache
     *
     * @return  Mage_Core_Model_Translate
     */
    protected function _saveCache()
    {
        if (!$this->_canUseCache()) {
            return $this;
        }
        Mage::app()->saveCache(serialize($this->getData()), $this->getCacheId(), array(self::CACHE_TAG), false);
        return $this;
    }

    /**
     * Check cache usage availability
     *
     * @return bool
     */
    protected function _canUseCache()
    {
        return Mage::app()->useCache(Mage_Core_Model_Cache_Type_Translate::TYPE_IDENTIFIER);
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
}
