<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Banner Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Model\Resource\Banner;

class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize banner resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Banner\Model\Banner', 'Magento\Banner\Model\Resource\Banner');
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
    }

    /**
     * Add stores column
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_column')) {
            $this->_addStoresVisibility();
        }
        $this->walk('getTypes');
        // fetch banner types from comma-separated
        return $this;
    }

    /**
     * Set add stores column flag
     *
     * @return $this
     */
    public function addStoresVisibility()
    {
        $this->setFlag('add_stores_column', true);
        return $this;
    }

    /**
     * Collect and set stores ids to each collection item
     * Used in banners grid as Visible in column info
     *
     * @return $this
     */
    protected function _addStoresVisibility()
    {
        $bannerIds = $this->getColumnValues('banner_id');
        $bannersStores = [];
        if (sizeof($bannerIds) > 0) {
            $adapter = $this->getConnection();
            $select = $adapter->select()->from(
                $this->getTable('magento_banner_content'),
                ['store_id', 'banner_id']
            )->where(
                'banner_id IN(?)',
                $bannerIds
            );
            $bannersRaw = $adapter->fetchAll($select);

            foreach ($bannersRaw as $banner) {
                if (!isset($bannersStores[$banner['banner_id']])) {
                    $bannersStores[$banner['banner_id']] = [];
                }
                $bannersStores[$banner['banner_id']][] = $banner['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($bannersStores[$item->getId()])) {
                $item->setStores($bannersStores[$item->getId()]);
            } else {
                $item->setStores([]);
            }
        }

        return $this;
    }

    /**
     * Add Filter by store
     *
     * @param int|array $storeIds
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($storeIds, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter')) {
            if ($withAdmin) {
                $storeIds = [0, $storeIds];
            }

            $this->getSelect()->join(
                ['store_table' => $this->getTable('magento_banner_content')],
                'main_table.banner_id = store_table.banner_id',
                []
            )->where(
                'store_table.store_id IN (?)',
                $storeIds
            )->group(
                'main_table.banner_id'
            );

            $this->setFlag('store_filter', true);
        }
        return $this;
    }

    /**
     * Add filter by banners
     *
     * @param array $bannerIds
     * @param bool $exclude
     * @return $this
     */
    public function addBannerIdsFilter($bannerIds, $exclude = false)
    {
        $this->addFieldToFilter('main_table.banner_id', [$exclude ? 'nin' : 'in' => $bannerIds]);
        return $this;
    }
}
