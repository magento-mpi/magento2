<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS block model
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Model\Resource\Block;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Cms\Model\Block', '\Magento\Cms\Model\Resource\Block');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs block_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('block_id', 'title');
    }

    /**
     * Add filter by store
     *
     * @param int|\Magento\Core\Model\Store $store
     * @param bool $withAdmin
     * @return \Magento\Cms\Model\Resource\Block\Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Core\Model\Store) {
            $store = array($store->getId());
        }

        if (!is_array($store)) {
            $store = array($store);
        }

        if ($withAdmin) {
            $store[] = \Magento\Core\Model\AppInterface::ADMIN_STORE_ID;
        }

        $this->addFilter('store', array('in' => $store), 'public');

        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();

        $countSelect->reset(\Zend_Db_Select::GROUP);

        return $countSelect;
    }

    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('cms_block_store')),
                'main_table.block_id = store_table.block_id',
                array()
            )->group('main_table.block_id');
        }
        return parent::_renderFiltersBefore();
    }

}
