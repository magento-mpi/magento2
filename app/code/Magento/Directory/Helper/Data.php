<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory data helper
 */
class Magento_Directory_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Config value that lists ISO2 country codes which have optional Zip/Postal pre-configured
     */
    const OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH = 'general/country/optional_zip_countries';

    /*
     * Path to config value, which lists countries, for which state is required.
     */
    const XML_PATH_STATES_REQUIRED = 'general/region/state_required';

    /*
     * Path to config value, which detects whether or not display the state for the country, if it is not required
     */
    const XML_PATH_DISPLAY_ALL_STATES = 'general/region/display_all';

    /**
     * Country collection
     *
     * @var Magento_Directory_Model_Resource_Country_Collection
     */
    protected $_countryCollection;

    /**
     * Region collection
     *
     * @var Magento_Directory_Model_Resource_Region_Collection
     */
    protected $_regionCollection;

    /**
     * Json representation of regions data
     *
     * @var string
     */
    protected $_regionJson;

    /**
     * Currency cache
     *
     * @var array
     */
    protected $_currencyCache = array();

    /**
     * ISO2 country codes which have optional Zip/Postal pre-configured
     *
     * @var array
     */
    protected $_optZipCountries = null;

    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @var Magento_Directory_Model_Resource_Region_CollectionFactory
     */
    protected $_regCollFactory;

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Directory_Model_Resource_Country_Collection $countryCollection
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regCollFactory,
     * @param Magento_Core_Helper_Data $coreHelper
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Directory_Model_Resource_Country_Collection $countryCollection,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regCollFactory,
        Magento_Core_Helper_Data $coreHelper,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Core_Model_Config $config
    ) {
        parent::__construct($context);
        $this->_configCacheType = $configCacheType;
        $this->_countryCollection = $countryCollection;
        $this->_regCollFactory = $regCollFactory;
        $this->_coreHelper = $coreHelper;
        $this->_storeManager = $storeManager;
        $this->_currencyFactory = $currencyFactory;
        $this->_config = $config;
    }

    /**
     * Retrieve region collection
     *
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = $this->_regCollFactory->create();
            $this->_regionCollection->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }

    /**
     * Retrieve country collection
     *
     * @return Magento_Directory_Model_Resource_Country_Collection
     */
    public function getCountryCollection()
    {
        if (!$this->_countryCollection->isLoaded()) {
            $this->_countryCollection->loadByStore();
        }
        return $this->_countryCollection;
    }

    /**
     * Retrieve regions data json
     *
     * @return string
     */
    public function getRegionJson()
    {
        Magento_Profiler::start('TEST: ' . __METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        if (!$this->_regionJson) {
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $countryIds = array();
                foreach ($this->getCountryCollection() as $country) {
                    $countryIds[] = $country->getCountryId();
                }
                $collection = $this->_regCollFactory->create();
                $collection->addCountryFilter($countryIds)
                    ->load();
                $regions = array(
                    'config' => array(
                        'show_all_regions' => $this->isShowNonRequiredState(),
                        'regions_required' => $this->getCountriesWithStatesRequired()
                    )
                );
                foreach ($collection as $region) {
                    /** @var $region Magento_Directory_Model_Region */
                    if (!$region->getRegionId()) {
                        continue;
                    }
                    $regions[$region->getCountryId()][$region->getRegionId()] = array(
                        'code' => $region->getCode(),
                        'name' => (string)__($region->getName())
                    );
                }
                $json = $this->_coreHelper->jsonEncode($regions);

                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_regionJson = $json;
        }

        Magento_Profiler::stop('TEST: ' . __METHOD__);
        return $this->_regionJson;
    }

    /**
     * Convert currency
     *
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function currencyConvert($amount, $from, $to = null)
    {
        if (empty($this->_currencyCache[$from])) {
            $this->_currencyCache[$from] = $this->_currencyFactory->create()->load($from);
        }
        if (is_null($to)) {
            $to = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        }
        $converted = $this->_currencyCache[$from]->convert($amount, $to);
        return $converted;
    }

    /**
     * Return ISO2 country codes, which have optional Zip/Postal pre-configured
     *
     * @param bool $asJson
     * @return array|string
     */
    public function getCountriesWithOptionalZip($asJson = false)
    {
        if (null === $this->_optZipCountries) {
            $value = trim($this->_storeManager->getStore()->getConfig(self::OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH));
            $this->_optZipCountries = preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);
        }
        if ($asJson) {
            return $this->_coreHelper->jsonEncode($this->_optZipCountries);
        }
        return $this->_optZipCountries;
    }

    /**
     * Check whether zip code is optional for specified country code
     *
     * @param string $countryCode
     * @return boolean
     */
    public function isZipCodeOptional($countryCode)
    {
        $this->getCountriesWithOptionalZip();
        return in_array($countryCode, $this->_optZipCountries);
    }

    /**
     * Returns the list of countries, for which region is required
     *
     * @param boolean $asJson
     * @return array
     */
    public function getCountriesWithStatesRequired($asJson = false)
    {
        $value = trim($this->_storeManager->getStore()->getConfig(self::XML_PATH_STATES_REQUIRED));
        $countryList =  preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);
        if ($asJson) {
            return $this->_coreHelper->jsonEncode($countryList);
        }
        return $countryList;
    }

    /**
     * Return, whether non-required state should be shown
     *
     * @return bool
     */
    public function isShowNonRequiredState()
    {
        return (boolean)$this->_storeManager->getStore()->getConfig(self::XML_PATH_DISPLAY_ALL_STATES);
    }

    /**
     * Returns flag, which indicates whether region is required for specified country
     *
     * @param string $countryId
     * @return bool
     */
    public function isRegionRequired($countryId)
    {
        $countyList = $this->getCountriesWithStatesRequired();
        if (!is_array($countyList)) {
            return false;
        }
        return in_array($countryId, $countyList);
    }

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_config->getValue(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE, 'default');
    }
}
