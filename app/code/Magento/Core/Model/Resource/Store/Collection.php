<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Stores collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Store_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'store_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'store_collection';

    /**
     *  Define resource model
     *
     */
    protected function _construct()
    {
        $this->setFlag('load_default_store', false);
        $this->_init('Magento_Core_Model_Store', 'Magento_Core_Model_Resource_Store');
    }

    /**
     * Set flag for load default (admin) store
     *
     * @param boolean $loadDefault
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function setLoadDefault($loadDefault)
    {
        $this->setFlag('load_default_store', (bool)$loadDefault);
        return $this;
    }

    /**
     * Is load default (admin) store
     *
     * @return boolean
     */
    public function getLoadDefault()
    {
        return $this->getFlag('load_default_store');
    }

    /**
     * Add disable default store filter to collection
     *
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function setWithoutDefaultFilter()
    {
        $this->addFieldToFilter('main_table.store_id', array('gt' => 0));
        return $this;
    }

    /**
     * Add filter by group id.
     * Group id can be passed as one single value or array of values.
     *
     * @param int|array $groupId
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function addGroupFilter($groupId)
    {
        return $this->addFieldToFilter('main_table.group_id', array('in' => $groupId));
    }

    /**
     * Add store id(s) filter to collection
     *
     * @param int|array $store
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function addIdFilter($store)
    {
        return $this->addFieldToFilter('main_table.store_id', array('in' => $store));
    }

    /**
     * Add filter by website to collection
     *
     * @param int|array $website
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function addWebsiteFilter($website)
    {
        return $this->addFieldToFilter('main_table.website_id', array('in' => $website));
    }

    /**
     * Add root category id filter to store collection
     *
     * @param int|array $category
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function addCategoryFilter($category)
    {
        if (!is_array($category)) {
            $category = array($category);
        }
        return $this->loadByCategoryIds($category);
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('store_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('store_id', 'name');
    }

    /**
     * Load collection data
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->getLoadDefault()) {
            $this->setWithoutDefaultFilter();
        }

        if (!$this->isLoaded()) {
            $this->addOrder('CASE WHEN main_table.store_id = 0 THEN 0 ELSE 1 END', Magento_DB_Select::SQL_ASC)
                ->addOrder('main_table.sort_order', Magento_DB_Select::SQL_ASC)
                ->addOrder('main_table.name', Magento_DB_Select::SQL_ASC);
        }
        return parent::load($printQuery, $logQuery);
    }

    /**
     * Add root category id filter to store collection
     *
     * @param array $categories
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function loadByCategoryIds(array $categories)
    {
        $this->addRootCategoryIdAttribute();
        $this->addFieldToFilter('group_table.root_category_id', array('in' => $categories));

        return $this;
    }

    /**
     * Add store root category data to collection
     *
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function addRootCategoryIdAttribute()
    {
        if (!$this->getFlag('core_store_group_table_joined')) {
            $this->getSelect()->join(
                array('group_table' => $this->getTable('core_store_group')),
                'main_table.group_id = group_table.group_id',
                array('root_category_id')
            );
            $this->setFlag('core_store_group_table_joined', true);
        }

        return $this;
    }
}
