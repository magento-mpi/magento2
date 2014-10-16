<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Helper;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Tax\Model\TaxClass\Source\Product as ProductTaxClassSource;
use Magento\Tax\Service\V1\TaxCalculationServiceInterface;

/**
 * Enterprise search helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper implements \Magento\Solr\Helper\ClientInterface
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
     * Product tax class source helper
     *
     * @var ProductTaxClassSource
     */
    protected $productTaxClassSource;

    /**
     * Tax calculation service
     *
     * @var TaxCalculationServiceInterface
     */
    protected $taxCalculationService;

    /**
     * Engine provider
     *
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Date time
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * Languages
     *
     * @var array
     */
    protected $_languages;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Tax\Helper\Data $taxData
     * @param ProductTaxClassSource $productTaxClassSource
     * @param TaxCalculationServiceInterface $taxCalculationService
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $supportedLanguages
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Tax\Helper\Data $taxData,
        ProductTaxClassSource $productTaxClassSource,
        TaxCalculationServiceInterface $taxCalculationService,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        CurrentCustomer $currentCustomer,
        array $supportedLanguages = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_taxData = $taxData;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->taxCalculationService = $taxCalculationService;
        $this->_scopeConfig = $scopeConfig;
        $this->_localeDate = $localeDate;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_languages = $supportedLanguages;
        $this->_localeResolver = $localeResolver;
        $this->currentCustomer = $currentCustomer;
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
        if ($engine == \Magento\Solr\Model\Adminhtml\System\Config\Source\Engine::SOLR) {
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
     * @param \Magento\Solr\Model\Resource\Collection $collection
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

            $format = $this->_localeDate->getDateFormat(
                \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT
            );
            if (is_array($value)) {
                foreach ($value as &$val) {
                    if (!$this->dateTime->isEmptyDate($val)) {
                        $date = new \Magento\Framework\Stdlib\DateTime\Date($val, $format);
                        $val = $date->toString(\Zend_Date::ISO_8601) . 'Z';
                    }
                }
            } else {
                if (!$this->dateTime->isEmptyDate($value)) {
                    $date = new \Magento\Framework\Stdlib\DateTime\Date($value, $format);
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
            $effectiveRates = [];
            if ($this->_taxData->priceIncludesTax() || !$this->_taxData->displayPriceExcludingTax()) {
                $defaultRates = array();
                $currentRates = array();
                foreach ($this->productTaxClassSource->getAllOptions() as $productTaxClass) {
                    $productTaxClassId = $productTaxClass['value'];
                    $customerId = $this->currentCustomer->getCustomerId();
                    $defaultRates[$productTaxClassId] = $this->taxCalculationService
                        ->getDefaultCalculatedRate(
                            $productTaxClassId,
                            $customerId
                        );
                    $currentRates[$productTaxClassId] = $this->taxCalculationService
                        ->getCalculatedRate(
                            $productTaxClassId,
                            $customerId
                        );
                }
                // Remove rate 0
                $defaultRates = array_filter($defaultRates);
                $currentRates = array_filter($currentRates);

                if ($this->_taxData->priceIncludesTax()) {
                    if ($defaultRates) {
                        $effectiveRates = array_merge($effectiveRates, $defaultRates);
                    }
                    if (!$this->_taxData->displayPriceExcludingTax() && $currentRates) {
                        $effectiveRates = array_merge($effectiveRates, $defaultRates);
                    }
                } else {
                    if ($this->_taxData->displayPriceIncludingTax()) {
                        if ($currentRates) {
                            $effectiveRates = array_merge($effectiveRates, $defaultRates);
                        }
                    }
                }
            }
            $this->_taxInfluence = !empty($effectiveRates);
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
