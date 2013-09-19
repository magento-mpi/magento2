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
 * \Directory data helper
 */
namespace Magento\Directory\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
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
     * @var \Magento\Directory\Model\Resource\Country\Collection
     */
    protected $_countryCollection;

    /**
     * Region collection
     *
     * @var \Magento\Directory\Model\Resource\Region\Collection
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
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Directory\Model\Resource\Region\Collection\Factory
     */
    protected $_regCollFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Country\Collection $countryCollection
     * @param \Magento\Directory\Model\Resource\Region\Collection\Factory $regCollFactory,
     * @param \Magento\Core\Helper\Data $coreHelper,
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Country\Collection $countryCollection,
        \Magento\Directory\Model\Resource\Region\Collection\Factory $regCollFactory,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_configCacheType = $configCacheType;
        $this->_countryCollection = $countryCollection;
        $this->_regCollFactory = $regCollFactory;
        $this->_coreHelper = $coreHelper;
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve region collection
     *
     * @return \Magento\Directory\Model\Resource\Region\Collection
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
     * @return \Magento\Directory\Model\Resource\Country\Collection
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

        \Magento\Profiler::start('TEST: ' . __METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
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
                    /** @var $region \Magento\Directory\Model\Region */
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

        \Magento\Profiler::stop('TEST: ' . __METHOD__);
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
            $this->_currencyCache[$from] = \Mage::getModel('Magento\Directory\Model\Currency')->load($from);
        }
        if (is_null($to)) {
            $to = \Mage::app()->getStore()->getCurrentCurrencyCode();
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
}
