<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping table rates collection
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Shipping_Model_Resource_Carrier_Tablerate_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * directory/country table name
     *
     * @var string
     */
    protected $_countryTable;

    /**
     * directory/country_region table name
     *
     * @var string
     */
    protected $_regionTable;

    /**
     * Define resource model and item
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Shipping_Model_Carrier_Tablerate', 'Magento_Shipping_Model_Resource_Carrier_Tablerate');
        $this->_countryTable    = $this->getTable('directory_country');
        $this->_regionTable     = $this->getTable('directory_country_region');
    }

    /**
     * Initialize select, add country iso3 code and region name
     *
     * @return void
     */
    public function _initSelect()
    {
        parent::_initSelect();

        $this->_select
            ->joinLeft(
                array('country_table' => $this->_countryTable),
                'country_table.country_id = main_table.dest_country_id',
                array('dest_country' => 'iso3_code'))
            ->joinLeft(
                array('region_table' => $this->_regionTable),
                'region_table.region_id = main_table.dest_region_id',
                array('dest_region' => 'code'));

        $this->addOrder('dest_country', self::SORT_ORDER_ASC);
        $this->addOrder('dest_region', self::SORT_ORDER_ASC);
        $this->addOrder('dest_zip', self::SORT_ORDER_ASC);
        $this->addOrder('condition_value', self::SORT_ORDER_ASC);
    }

    /**
     * Add website filter to collection
     *
     * @param int $websiteId
     * @return Magento_Shipping_Model_Resource_Carrier_Tablerate_Collection
     */
    public function setWebsiteFilter($websiteId)
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * Add condition name (code) filter to collection
     *
     * @param string $conditionName
     * @return Magento_Shipping_Model_Resource_Carrier_Tablerate_Collection
     */
    public function setConditionFilter($conditionName)
    {
        return $this->addFieldToFilter('condition_name', $conditionName);
    }

    /**
     * Add country filter to collection
     *
     * @param string $countryId
     * @return Magento_Shipping_Model_Resource_Carrier_Tablerate_Collection
     */
    public function setCountryFilter($countryId)
    {
        return $this->addFieldToFilter('dest_country_id', $countryId);
    }
}
