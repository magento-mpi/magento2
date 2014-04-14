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
 * Directory Country Resource Collection
 */
namespace Magento\Directory\Model\Resource\Country;

class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Locale model
     *
     * @var \Magento\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Directory\Model\Resource\CountryFactory
     */
    protected $_countryFactory;

    /**
     * Array utils object
     *
     * @var \Magento\Stdlib\ArrayUtils
     */
    protected $_arrayUtils;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Locale\ListsInterface $localeLists
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Model\Resource\CountryFactory $countryFactory
     * @param \Magento\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param mixed $connection
     * @param \Magento\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Locale\ListsInterface $localeLists,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\Resource\CountryFactory $countryFactory,
        \Magento\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Locale\ResolverInterface $localeResolver,
        $connection = null,
        \Magento\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_scopeConfig = $scopeConfig;
        $this->_localeLists = $localeLists;
        $this->_localeResolver = $localeResolver;
        $this->_countryFactory = $countryFactory;
        $this->_arrayUtils = $arrayUtils;
    }

    /**
     * Foreground countries
     *
     * @var array
     */
    protected $_foregroundCountries = array();

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Directory\Model\Country', 'Magento\Directory\Model\Resource\Country');
    }

    /**
     * Load allowed countries for current store
     *
     * @param mixed $store
     * @return \Magento\Directory\Model\Resource\Country\Collection
     */
    public function loadByStore($store = null)
    {
        $allowCountries = explode(',', (string)$this->_scopeConfig->getValue('general/country/allow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store));
        if (!empty($allowCountries)) {
            $this->addFieldToFilter("country_id", array('in' => $allowCountries));
        }
        return $this;
    }

    /**
     * Loads Item By Id
     *
     * @param string $countryId
     * @return \Magento\Directory\Model\Resource\Country
     */
    public function getItemById($countryId)
    {
        foreach ($this->_items as $country) {
            if ($country->getCountryId() == $countryId) {
                return $country;
            }
        }
        return $this->_countryFactory->create();
    }

    /**
     * Add filter by country code to collection.
     * $countryCode can be either array of country codes or string representing one country code.
     * $iso can be either array containing 'iso2', 'iso3' values or string with containing one of that values directly.
     * The collection will contain countries where at least one of contry $iso fields matches $countryCode.
     *
     * @param string|string[] $countryCode
     * @param string|string[] $iso
     * @return $this
     */
    public function addCountryCodeFilter($countryCode, $iso = array('iso3', 'iso2'))
    {
        if (!empty($countryCode)) {
            if (is_array($countryCode)) {
                if (is_array($iso)) {
                    $whereOr = array();
                    foreach ($iso as $iso_curr) {
                        $whereOr[] .= $this->_getConditionSql("{$iso_curr}_code", array('in' => $countryCode));
                    }
                    $this->_select->where('(' . implode(') OR (', $whereOr) . ')');
                } else {
                    $this->addFieldToFilter("{$iso}_code", array('in' => $countryCode));
                }
            } else {
                if (is_array($iso)) {
                    $whereOr = array();
                    foreach ($iso as $iso_curr) {
                        $whereOr[] .= $this->_getConditionSql("{$iso_curr}_code", $countryCode);
                    }
                    $this->_select->where('(' . implode(') OR (', $whereOr) . ')');
                } else {
                    $this->addFieldToFilter("{$iso}_code", $countryCode);
                }
            }
        }
        return $this;
    }

    /**
     * Add filter by country code(s) to collection
     *
     * @param string|string[] $countryId
     * @return $this
     */
    public function addCountryIdFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter("country_id", array('in' => $countryId));
            } else {
                $this->addFieldToFilter("country_id", $countryId);
            }
        }
        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @param string|boolean $emptyLabel
     * @return array
     */
    public function toOptionArray($emptyLabel = ' ')
    {
        $options = $this->_toOptionArray('country_id', 'name', array('title' => 'iso2_code'));

        $sort = array();
        foreach ($options as $data) {
            $name = (string)$this->_localeLists->getCountryTranslation($data['value']);
            if (!empty($name)) {
                $sort[$name] = $data['value'];
            }
        }
        $this->_arrayUtils->ksortMultibyte($sort, $this->_localeResolver->getLocaleCode());
        foreach (array_reverse($this->_foregroundCountries) as $foregroundCountry) {
            $name = array_search($foregroundCountry, $sort);
            unset($sort[$name]);
            $sort = array($name => $foregroundCountry) + $sort;
        }
        $options = array();
        foreach ($sort as $label => $value) {
            $options[] = array('value' => $value, 'label' => $label);
        }

        if (count($options) > 0 && $emptyLabel !== false) {
            array_unshift($options, array('value' => '', 'label' => $emptyLabel));
        }

        return $options;
    }

    /**
     * Set foreground countries array
     *
     * @param string|array $foregroundCountries
     * @return $this
     */
    public function setForegroundCountries($foregroundCountries)
    {
        if (empty($foregroundCountries)) {
            return $this;
        }
        $this->_foregroundCountries = (array)$foregroundCountries;
        return $this;
    }
}
