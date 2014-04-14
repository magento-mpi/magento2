<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Helper;

/**
 * Enterprise search helper
 */
class Data extends \Magento\App\Helper\AbstractHelper implements \Magento\Search\Helper\ClientInterface
{
    /**
     * Define if search engine is used for layered navigation
     *
     * @var bool|null
     */
    protected $_useEngineInLayeredNavigation = null;

    /**
     * Store languag codes for local codes
     *
     * @var array
     */
    protected $_languageCode = array();

    /**
     * Store result of third party search engine availability check
     *
     * @var bool|null
     */
    protected $_isThirdPartyEngineAvailable = null;

    /**
     * Show if taxes have influence on price
     *
     * @var bool|null
     */
    protected $_taxInfluence = null;

    /**
     * Define if engine is available for layered navigation
     *
     * @var bool|null
     */
    protected $_isEngineAvailableForNavigation = null;

    /**
     * Define text type fields
     *
     * @var string[]
     */
    protected $_textFieldTypes = array('text', 'varchar');

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;

    /**
     * Engine provider
     *
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Date time
     *
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * Languages
     *
     * @var array
     */
    protected $_languages;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param array $supportedLanguages
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Locale\ResolverInterface $localeResolver,
        array $supportedLanguages = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_taxData = $taxData;
        $this->_scopeConfig = $scopeConfig;
        $this->_localeDate = $localeDate;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_languages = $supportedLanguages;
        $this->_localeResolver = $localeResolver;
        parent::__construct($context);
    }

    /**
     * Retrieve text field types
     *
     * @return string[]
     */
    public function getTextFieldTypes()
    {
        return $this->_textFieldTypes;
    }

    /**
     * Retrieve supported by Solr languages including locale codes (language codes) that are specified in configuration
     * Array(
     *      'language_code1' => 'locale_code',
     *      'language_code2' => Array('locale_code1', 'locale_code2')
     * )
     *
     * @return array
     */
    public function getSolrSupportedLanguages()
    {
        $default = array(
            'da' => 'da_DK',
            'nl' => 'nl_NL',
            'en' => array('en_AU', 'en_CA', 'en_NZ', 'en_GB', 'en_US'),
            'fi' => 'fi_FI',
            'fr' => array('fr_CA', 'fr_FR'),
            'de' => array('de_DE', 'de_CH', 'de_AT'),
            'it' => array('it_IT', 'it_CH'),
            'nb' => array('nb_NO', 'nn_NO'),
            'pt' => array('pt_BR', 'pt_PT'),
            'ro' => 'ro_RO',
            'ru' => 'ru_RU',
            'es' => array('es_AR', 'es_CL', 'es_CO', 'es_CR', 'es_ES', 'es_MX', 'es_PA', 'es_PE', 'es_VE'),
            'sv' => 'sv_SE',
            'tr' => 'tr_TR',
            'cs' => 'cs_CZ',
            'el' => 'el_GR',
            'th' => 'th_TH',
            'zh' => array('zh_CN', 'zh_HK', 'zh_TW'),
            'ja' => 'ja_JP',
            'ko' => 'ko_KR'
        );

        /**
         * Merging languages that specified manually
         */
        foreach ($this->_languages as $localeCode => $langCode) {
            if (isset($default[$langCode])) {
                if (is_array($default[$langCode])) {
                    if (!in_array($localeCode, $default[$langCode])) {
                        $default[$langCode][] = $localeCode;
                    }
                } elseif ($default[$langCode] != $localeCode) {
                    $default[$langCode] = array($default[$langCode], $localeCode);
                }
            } else {
                $default[$langCode] = $localeCode;
            }
        }


        return $default;
    }

    /**
     * Retrieve information from Solr search engine configuration
     *
     * @param string $field
     * @param int $storeId
     * @return string|int
     */
    public function getSolrConfigData($field, $storeId = null)
    {
        return $this->getSearchConfigData('solr_' . $field, $storeId);
    }

    /**
     * Retrieve information from search engine configuration
     *
     * @param string $field
     * @param int|null $storeId
     * @return string|int
     */
    public function getSearchConfigData($field, $storeId = null)
    {
        $path = 'catalog/search/' . $field;
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Return true if third party search engine is used
     *
     * @return bool
     */
    public function isThirdPartSearchEngine()
    {
        $engine = $this->getSearchConfigData('engine');
        if ($engine == \Magento\Search\Model\Adminhtml\System\Config\Source\Engine::SOLR) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve language code by specified locale code if this locale is supported
     *
     * @param  string $localeCode
     * @return string|false
     */
    public function getLanguageCodeByLocaleCode($localeCode)
    {
        $localeCode = (string)$localeCode;
        if (!$localeCode) {
            return false;
        }

        if (!isset($this->_languageCode[$localeCode])) {
            $languages = $this->getSolrSupportedLanguages();

            $this->_languageCode[$localeCode] = false;
            foreach ($languages as $code => $locales) {
                if (is_array($locales)) {
                    if (in_array($localeCode, $locales)) {
                        $this->_languageCode[$localeCode] = $code;
                    }
                } elseif ($localeCode == $locales) {
                    $this->_languageCode[$localeCode] = $code;
                }
            }
        }

        return $this->_languageCode[$localeCode];
    }

    /**
     * Prepare language suffix for text fields.
     * For not supported languages prefix _def will be returned.
     *
     * @param  string $localeCode
     * @return string
     */
    public function getLanguageSuffix($localeCode)
    {
        $languageCode = $this->getLanguageCodeByLocaleCode($localeCode);
        if (!$languageCode) {
            $languageCode = 'def';
        }
        $languageSuffix = '_' . $languageCode;

        return $languageSuffix;
    }

    /**
     * Retrieve filter array
     *
     * @param \Magento\Search\Model\Resource\Collection $collection
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param string|array $value
     * @return false|array
     * @deprecated since 1.12.0.0
     */
    public function getSearchParam($collection, $attribute, $value)
    {
        if (empty($value) || isset(
            $value['from']
        ) && empty($value['from']) && isset(
            $value['to']
        ) && empty($value['to'])
        ) {
            return false;
        }

        $locale = $this->_scopeConfig->getValue(
            $this->_localeResolver->getDefaultLocalePath(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $languageSuffix = $this->getLanguageSuffix($locale);

        $field = $attribute->getAttributeCode();
        $backendType = $attribute->getBackendType();
        $frontendInput = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_' . $field;
        } elseif ($backendType == 'decimal') {
            $field = 'attr_decimal_' . $field;
        } elseif ($frontendInput == 'select' || $frontendInput == 'boolean') {
            $field = 'attr_select_' . $field;
        } elseif ($backendType == 'datetime') {
            $field = 'attr_datetime_' . $field;

            $format = $this->_localeDate->getDateFormat(\Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);
            if (is_array($value)) {
                foreach ($value as &$val) {
                    if (!$this->dateTime->isEmptyDate($val)) {
                        $date = new \Magento\Stdlib\DateTime\Date($val, $format);
                        $val = $date->toString(\Zend_Date::ISO_8601) . 'Z';
                    }
                }
            } else {
                if (!$this->dateTime->isEmptyDate($value)) {
                    $date = new \Magento\Stdlib\DateTime\Date($value, $format);
                    $value = $date->toString(\Zend_Date::ISO_8601) . 'Z';
                }
            }
        } elseif (in_array($backendType, $this->_textFieldTypes)) {
            $field .= $languageSuffix;
        }

        if ($attribute->usesSource()) {
            $attribute->setStoreId($this->_storeManager->getStore()->getId());
        }

        return array($field => $value);
    }

    /**
     * Check if enterprise engine is available
     *
     * @return bool
     */
    public function isActiveEngine()
    {
        $engine = $this->_engineProvider->get();
        return is_object($engine) && $engine->allowAdvancedIndex();
    }

    /**
     * Check if third party engine is selected and active
     *
     * @return bool
     */
    public function isThirdPartyEngineAvailable()
    {
        if ($this->_isThirdPartyEngineAvailable === null) {
            $this->_isThirdPartyEngineAvailable = $this->isThirdPartSearchEngine() && $this->isActiveEngine();
        }

        return $this->_isThirdPartyEngineAvailable;
    }

    /**
     * Check if taxes have influence on price
     *
     * @return bool
     */
    public function getTaxInfluence()
    {
        if (is_null($this->_taxInfluence)) {
            $this->_taxInfluence = (bool)$this->_taxData->getPriceTaxSql('price', 'tax');
        }

        return $this->_taxInfluence;
    }

    /**
     * Check if search engine can be used for catalog navigation
     *
     * @param bool $isCatalog - define if checking availability for catalog navigation or search result navigation
     * @return bool
     */
    public function getIsEngineAvailableForNavigation($isCatalog = true)
    {
        if (is_null($this->_isEngineAvailableForNavigation)) {
            $this->_isEngineAvailableForNavigation = false;
            if ($this->isActiveEngine()) {
                if ($isCatalog) {
                    if ($this->getSearchConfigData(
                        'solr_server_use_in_catalog_navigation'
                    ) && !$this->getTaxInfluence()
                    ) {
                        $this->_isEngineAvailableForNavigation = true;
                    }
                } else {
                    $this->_isEngineAvailableForNavigation = true;
                }
            }
        }

        return $this->_isEngineAvailableForNavigation;
    }

    /**
     * Return search client options
     *
     * @param array $options
     * @return mixed
     */
    public function prepareClientOptions($options = array())
    {
        $def_options = array(
            'hostname' => $this->getSolrConfigData('server_hostname'),
            'login' => $this->getSolrConfigData('server_username'),
            'password' => $this->getSolrConfigData('server_password'),
            'port' => $this->getSolrConfigData('server_port'),
            'timeout' => $this->getSolrConfigData('server_timeout'),
            'path' => $this->getSolrConfigData('server_path')
        );
        $options = array_merge($def_options, $options);
        return $options;
    }

    // Deprecated methods

    /**
     * Retrieve attribute field's name
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @return string
     * @deprecated after 1.11.2.0
     */
    public function getAttributeSolrFieldName($attribute)
    {
        return '';
    }
}
