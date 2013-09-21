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
 * Country collection
 */
class Magento_Directory_Model_Resource_Region_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Locale region name table name
     *
     * @var string
     */
    protected $_regionNameTable;

    /**
     * Country table name
     *
     * @var string
     */
    protected $_countryTable;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct(
            $eventManager, $logger, $fetchStrategy, $entityFactory, $resource
        );
        $this->_locale = $locale;
    }

    /**
     * Define main, country, locale region name tables
     */
    protected function _construct()
    {
        $this->_init('Magento_Directory_Model_Region', 'Magento_Directory_Model_Resource_Region');

        $this->_countryTable    = $this->getTable('directory_country');
        $this->_regionNameTable = $this->getTable('directory_country_region_name');

        $this->addOrder('name', Magento_Data_Collection::SORT_ORDER_ASC);
        $this->addOrder('default_name', Magento_Data_Collection::SORT_ORDER_ASC);
    }

    /**
     * Initialize select object
     *
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $locale = $this->_locale->getLocaleCode();

        $this->addBindParam(':region_locale', $locale);
        $this->getSelect()->joinLeft(
            array('rname' => $this->_regionNameTable),
            'main_table.region_id = rname.region_id AND rname.locale = :region_locale',
            array('name'));

        return $this;
    }

    /**
     * Filter by country_id
     *
     * @param string|array $countryId
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function addCountryFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('main_table.country_id', array('in' => $countryId));
            } else {
                $this->addFieldToFilter('main_table.country_id', $countryId);
            }
        }
        return $this;
    }

    /**
     * Filter by country code (ISO 3)
     *
     * @param string $countryCode
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function addCountryCodeFilter($countryCode)
    {
        $this->getSelect()
            ->joinLeft(
                array('country' => $this->_countryTable),
                'main_table.country_id = country.country_id'
            )
            ->where('country.iso3_code = ?', $countryCode);

        return $this;
    }

    /**
     * Filter by Region code
     *
     * @param string|array $regionCode
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function addRegionCodeFilter($regionCode)
    {
        if (!empty($regionCode)) {
            if (is_array($regionCode)) {
                $this->addFieldToFilter('main_table.code', array('in' => $regionCode));
            } else {
                $this->addFieldToFilter('main_table.code', $regionCode);
            }
        }
        return $this;
    }

    /**
     * Filter by region name
     *
     * @param string|array $regionName
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function addRegionNameFilter($regionName)
    {
        if (!empty($regionName)) {
            if (is_array($regionName)) {
                $this->addFieldToFilter('main_table.default_name', array('in' => $regionName));
            } else {
                $this->addFieldToFilter('main_table.default_name', $regionName);
            }
        }
        return $this;
    }

    /**
     * Filter region by its code or name
     *
     * @param string|array $region
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    public function addRegionCodeOrNameFilter($region)
    {
        if (!empty($region)) {
            $condition = is_array($region) ? array('in' => $region) : $region;
            $this->addFieldToFilter(array('main_table.code', 'main_table.default_name'), array($condition, $condition));
        }
        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_toOptionArray('region_id', 'default_name', array('title' => 'default_name'));
        if (count($options) > 0) {
            array_unshift($options, array(
                'title '=> null,
                'value' => '0',
                'label' => __('--Please select--')
            ));
        }
        return $options;
    }
}
