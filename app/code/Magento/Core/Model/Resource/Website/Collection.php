<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Website;

/**
 * Websites collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Map field to alias
     *
     * @var array
     */
    protected $_map = array('fields' => array('website_id' => 'main_table.website_id'));

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setFlag('load_default_website', false);
        $this->_init('Magento\Core\Model\Website', 'Magento\Core\Model\Resource\Website');
    }

    /**
     * Apply custom filtering
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if (!$this->getLoadDefault()) {
            $this->getSelect()->where('main_table.website_id > ?', 0);
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Set flag for load default (admin) website
     *
     * @param bool $loadDefault
     * @return $this
     */
    public function setLoadDefault($loadDefault)
    {
        $this->setFlag('load_default_website', (bool)$loadDefault);
        return $this;
    }

    /**
     * Is load default (admin) website
     *
     * @return bool
     */
    public function getLoadDefault()
    {
        return $this->getFlag('load_default_website');
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('website_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('website_id', 'name');
    }

    /**
     * Add website filter to collection
     *
     * @param int $ids|array
     * @return $this
     */
    public function addIdFilter($ids)
    {
        if (is_array($ids)) {
            if (empty($ids)) {
                $this->addFieldToFilter('website_id', null);
            } else {
                $this->addFieldToFilter('website_id', array('in' => $ids));
            }
        } else {
            $this->addFieldToFilter('website_id', $ids);
        }
        return $this;
    }

    /**
     * Load collection data
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        $this->unshiftOrder(
            'main_table.name',
            \Magento\DB\Select::SQL_ASC
        )->unshiftOrder(
            'main_table.sort_order',
            \Magento\DB\Select::SQL_ASC
        );
        // website sort order FIRST

        return parent::load($printQuery, $logQuery);
    }

    /**
     * Join group and store info from appropriate tables.
     * Defines new _idFiledName as 'website_group_store' bc for
     * one website can be more then one row in collection.
     * Sets extra combined ordering by group's name, defined
     * sort ordering and store's name.
     *
     * @return $this
     */
    public function joinGroupAndStore()
    {
        if (!$this->getFlag('groups_and_stores_joined')) {
            $this->_idFieldName = 'website_group_store';
            $this->getSelect()->joinLeft(
                array('group_table' => $this->getTable('core_store_group')),
                'main_table.website_id = group_table.website_id',
                array('group_id' => 'group_id', 'group_title' => 'name')
            )->joinLeft(
                array('store_table' => $this->getTable('core_store')),
                'group_table.group_id = store_table.group_id',
                array('store_id' => 'store_id', 'store_title' => 'name')
            );
            $this->addOrder(
                'group_table.name',
                \Magento\DB\Select::SQL_ASC
            )->addOrder(
                'CASE WHEN store_table.store_id = 0 THEN 0 ELSE 1 END',
                \Magento\DB\Select::SQL_ASC
            )->addOrder(
                'store_table.sort_order',
                \Magento\DB\Select::SQL_ASC
            )->addOrder(
                'store_table.name',
                \Magento\DB\Select::SQL_ASC
            );
            $this->setFlag('groups_and_stores_joined', true);
        }
        return $this;
    }

    /**
     * Adding filter by group id or array of ids but only if
     * tables with appropriate information were joined before.
     *
     * @param int|array $groupIds
     * @return $this
     */
    public function addFilterByGroupIds($groupIds)
    {
        if ($this->getFlag('groups_and_stores_joined')) {
            $this->addFieldToFilter('group_table.group_id', $groupIds);
        }
        return $this;
    }
}
