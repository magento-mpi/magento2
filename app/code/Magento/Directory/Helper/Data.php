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
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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
    protected $_optionalZipCountries = null;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Cache\Type\Config $configCacheType
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context);
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Retrieve region collection
     *
     * @return \Magento\Directory\Model\Resource\Region\Collection
     */
    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = \Mage::getModel('Magento\Directory\Model\Region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
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
        if (!$this->_countryCollection) {
            $this->_countryCollection = \Mage::getModel('Magento\Directory\Model\Country')->getResourceCollection()
                ->loadByStore();
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

        \Magento\Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        if (!$this->_regionJson) {
            $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . \Mage::app()->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $countryIds = array();
                foreach ($this->getCountryCollection() as $country) {
                    $countryIds[] = $country->getCountryId();
                }
                $collection = \Mage::getModel('Magento\Directory\Model\Region')->getResourceCollection()
                    ->addCountryFilter($countryIds)
                    ->load();
                $regions = array(
                    'config' => array(
                        'show_all_regions' => $this->getShowNonRequiredState(),
                        'regions_required' => $this->getCountriesWithStatesRequired()
                    )
                );
                foreach ($collection as $region) {
                    if (!$region->getRegionId()) {
                        continue;
                    }
                    $regions[$region->getCountryId()][$region->getRegionId()] = array(
                        'code' => $region->getCode(),
                        'name' => __($region->getName())
                    );
                }
                $json = $this->_coreData->jsonEncode($regions);

                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_regionJson = $json;
        }

        \Magento\Profiler::stop('TEST: '.__METHOD__);
        return $this->_regionJson;
    }

    /**
     * Convert currency
     *
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
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
        if (null === $this->_optionalZipCountries) {
            $this->_optionalZipCountries = preg_split('/\,/',
                \Mage::getStoreConfig(self::OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH), 0, PREG_SPLIT_NO_EMPTY);
        }
        if ($asJson) {
            return $this->_coreData->jsonEncode($this->_optionalZipCountries);
        }
        return $this->_optionalZipCountries;
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
        return in_array($countryCode, $this->_optionalZipCountries);
    }

    /**
     * Returns the list of countries, for which region is required
     *
     * @param boolean $asJson
     * @return array
     */
    public function getCountriesWithStatesRequired($asJson = false)
    {
        $countryList = explode(',', \Mage::getStoreConfig(self::XML_PATH_STATES_REQUIRED));
        if ($asJson) {
            return $this->_coreData->jsonEncode($countryList);
        }
        return $countryList;
    }

    /**
     * Return flag, which indicates whether or not non required state should be shown
     *
     * @return bool
     */
    public function getShowNonRequiredState()
    {
        return (boolean)\Mage::getStoreConfig(self::XML_PATH_DISPLAY_ALL_STATES);
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
        if(!is_array($countyList)) {
            return false;
        }
        return in_array($countryId, $countyList);
    }
}
