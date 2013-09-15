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
 * \Directory Country Resource Collection
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model\Resource\Country;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * String helper
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_stringHelper;

    /**
     * Locale model
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param \Magento\Core\Helper\String $stringHelper
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        \Magento\Core\Helper\String $stringHelper,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        $resource = null
    ) {
        parent::__construct($eventManager, $fetchStrategy, $resource);
        $this->_stringHelper = $stringHelper;
        $this->_locale = $locale;
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
        $allowCountries = explode(',', (string)\Mage::getStoreConfig('general/country/allow', $store));
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
        return \Mage::getResourceModel('Magento\Directory\Model\Resource\Country');
    }

    /**
     * Add filter by country code to collection.
     * $countryCode can be either array of country codes or string representing one country code.
     * $iso can be either array containing 'iso2', 'iso3' values or string with containing one of that values directly.
     * The collection will contain countries where at least one of contry $iso fields matches $countryCode.
     *
     * @param string|array $countryCode
     * @param string|array $iso
     * @return \Magento\Directory\Model\Resource\Country\Collection
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
                    $this->addFieldToFilter("{$iso}_code", array('in'=>$countryCode));
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
     * @param string|array $countryId
     * @return \Magento\Directory\Model\Resource\Country\Collection
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
        $options = $this->_toOptionArray('country_id', 'name', array('title'=>'iso2_code'));

        $sort = array();
        foreach ($options as $data) {
            $name = $this->_locale->getCountryTranslation($data['value']);
            if (!empty($name)) {
                $sort[$name] = $data['value'];
            }
        }
        $this->_stringHelper->ksortMultibyte($sort);
        foreach (array_reverse($this->_foregroundCountries) as $foregroundCountry) {
            $name = array_search($foregroundCountry, $sort);
            unset($sort[$name]);
            $sort = array($name => $foregroundCountry) + $sort;
        }
        $options = array();
        foreach ($sort as $label => $value) {
            $options[] = array(
               'value' => $value,
               'label' => $label
            );
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
     * @return \Magento\Directory\Model\Resource\Country\Collection
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
