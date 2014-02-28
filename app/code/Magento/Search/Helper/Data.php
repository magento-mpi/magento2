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
    protected $_useEngineInLayeredNavigation    = null;

    /**
     * Store languag codes for local codes
     *
     * @var array
     */
    protected $_languageCode                    = array();

    /**
     * Store result of third party search engine availability check
     *
     * @var bool|null
     */
    protected $_isThirdPartyEngineAvailable     = null;

    /**
     * Show if taxes have influence on price
     *
     * @var bool|null
     */
    protected $_taxInfluence                    = null;

    /**
     * Define if engine is available for layered navigation
     *
     * @var bool|null
     */
    protected $_isEngineAvailableForNavigation  = null;

    /**
     * Define text type fields
     *
     * @var array
     */
    protected $_textFieldTypes = array(
        'text',
        'varchar'
    );

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * Locale
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var array
     */
    protected $_languages;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param array $supportedLanguages
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Stdlib\DateTime $dateTime,
        array $supportedLanguages = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_taxData = $taxData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_languages = $supportedLanguages;
        parent::__construct($context);
    }

    /**
     * Retrieve text field types
     *
     * @return array
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
            /**
             * SnowBall filter based
             */
            //Danish
            'da' => 'da_DK',
            //Dutch
            'nl' => 'nl_NL',
            //English
            'en' => array('en_AU', 'en_CA', 'en_NZ', 'en_GB', 'en_US'),
            //Finnish
            'fi' => 'fi_FI',
            //French
            'fr' => array('fr_CA', 'fr_FR'),
            //German
            'de' => array('de_DE','de_CH','de_AT'),
            //Italian
            'it' => array('it_IT','it_CH'),
            //Norwegian
            'nb' => array('nb_NO', 'nn_NO'),
            //Portuguese
            'pt' => array('pt_BR', 'pt_PT'),
            //Romanian
            'ro' => 'ro_RO',
            //Russian
            'ru' => 'ru_RU',
            //Spanish
            'es' => array('es_AR', 'es_CL', 'es_CO', 'es_CR', 'es_ES', 'es_MX', 'es_PA', 'es_PE', 'es_VE'),
            //Swedish
            'sv' => 'sv_SE',
            //Turkish
            'tr' => 'tr_TR',

            /**
             * Lucene class based
             */
            //Czech
            'cs' => 'cs_CZ',
            //Greek
            'el' => 'el_GR',
            //Thai
            'th' => 'th_TH',
            //Chinese
            'zh' => array('zh_CN', 'zh_HK', 'zh_TW'),
            //Japanese
            'ja' => 'ja_JP',
            //Korean
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
     * @param int $storeId
     * @return string|int
     */
    public function getSearchConfigData($field, $storeId = null)
    {
        $path = 'catalog/search/' . $field;
        return $this->_coreStoreConfig->getConfig($path, $storeId);
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
     * @deprecated since 1.12.0.0
     *
     * @param \Magento\Search\Model\Resource\Collection $collection
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param string|array $value
     * @return array
     */
    public function getSearchParam($collection, $attribute, $value)
    {
        if (empty($value)
            || (isset($value['from']) && empty($value['from'])
                && isset($value['to']) && empty($value['to'])
            )
        ) {
            return false;
        }

        $locale = $this->_storeManager->getStore()
            ->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE);
        $languageSuffix = $this->getLanguageSuffix($locale);

        $field = $attribute->getAttributeCode();
        $backendType = $attribute->getBackendType();
        $frontendInput = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_'. $field;
        } elseif ($backendType == 'decimal') {
            $field = 'attr_decimal_'. $field;
        } elseif ($frontendInput == 'select' || $frontendInput == 'boolean') {
            $field = 'attr_select_'. $field;
        } elseif ($backendType == 'datetime') {
            $field = 'attr_datetime_'. $field;

            $format = $this->_locale->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
            if (is_array($value)) {
                foreach ($value as &$val) {
                    if (!$this->dateTime->isEmptyDate($val)) {
                        $date = new \Zend_Date($val, $format);
                        $val = $date->toString(\Zend_Date::ISO_8601) . 'Z';
                    }
                }
            } else {
                if (!$this->dateTime->isEmptyDate($value)) {
                    $date = new \Zend_Date($value, $format);
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
            $this->_isThirdPartyEngineAvailable = ($this->isThirdPartSearchEngine() && $this->isActiveEngine());
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
            $this->_taxInfluence = (bool) $this->_taxData->getPriceTaxSql('price', 'tax');
        }

        return $this->_taxInfluence;
    }

    /**
     * Check if search engine can be used for catalog navigation
     *
     * @param   bool $isCatalog - define if checking availability for catalog navigation or search result navigation
     * @return  bool
     */
    public function getIsEngineAvailableForNavigation($isCatalog = true)
    {
        if (is_null($this->_isEngineAvailableForNavigation)) {
            $this->_isEngineAvailableForNavigation = false;
            if ($this->isActiveEngine()) {
                if ($isCatalog) {
                    if ($this->getSearchConfigData('solr_server_use_in_catalog_navigation')
                        && !$this->getTaxInfluence()
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
     * @param $options
     * @return mixed
     */
    public function prepareClientOptions($options = array())
    {
        $def_options = array(
            'hostname' => $this->getSolrConfigData('server_hostname'),
            'login'    => $this->getSolrConfigData('server_username'),
            'password' => $this->getSolrConfigData('server_password'),
            'port'     => $this->getSolrConfigData('server_port'),
            'timeout'  => $this->getSolrConfigData('server_timeout'),
            'path'     => $this->getSolrConfigData('server_path')
        );
        $options = array_merge($def_options, $options);
        return $options;
    }



    // Deprecated methods

    /**
     * Retrieve attribute field's name
     *
     * @deprecated after 1.11.2.0
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     *
     * @return string
     */
    public function getAttributeSolrFieldName($attribute)
    {
        return '';
    }
}
