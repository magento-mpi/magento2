<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Popular tags collection model
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Popular_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Defines resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Tag_Model_Tag', 'Magento_Tag_Model_Resource_Tag');
    }

    /**
     * Replacing popularity by sum of popularity and base_popularity
     *
     * @param int $storeId
     * @return Magento_Tag_Model_Resource_Popular_Collection
     */
    public function joinFields($storeId = 0)
    {
        $this->getSelect()
            ->reset()
            ->from(
                array('tag_summary' => $this->getTable('tag_summary')),
                array('popularity' => 'tag_summary.popularity'))
            ->joinInner(
                array('tag' => $this->getTable('tag')),
                'tag.tag_id = tag_summary.tag_id AND tag.status = ' . Magento_Tag_Model_Tag::STATUS_APPROVED)
            ->where('tag_summary.store_id = ?', $storeId)
            ->where('tag_summary.products > ?', 0)
            ->order('popularity ' . Magento_DB_Select::SQL_DESC);

        return $this;
    }

    /**
     * Add filter by specified tag status
     *
     * @param string $statusCode
     * @return Magento_Tag_Model_Resource_Popular_Collection
     */
    public function addStatusFilter($statusCode)
    {
        $this->getSelect()->where('main_table.status = ?', $statusCode);
        return $this;
    }

    /**
     * Loads collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Magento_Tag_Model_Resource_Popular_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        return $this;
    }

    /**
     * Sets limit
     *
     * @param int $limit
     * @return Magento_Tag_Model_Resource_Popular_Collection
     */
    public function limit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Magento_DB_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $countSelect = $this->getConnection()->select();
        $countSelect->from(array('a' => $select), 'COUNT(popularity)');
        return $countSelect;
    }
}
