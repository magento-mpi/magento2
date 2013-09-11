<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Banner Resource Collection
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Model\Resource\Banner;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize banner resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Banner\Model\Banner', '\Magento\Banner\Model\Resource\Banner');
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
    }

    /**
     * Add stores column
     *
     * @return \Magento\Banner\Model\Resource\Banner\Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_column')) {
            $this->_addStoresVisibility();
        }
        $this->walk('getTypes'); // fetch banner types from comma-separated
        return $this;
    }

    /**
     * Set add stores column flag
     *
     * @return \Magento\Banner\Model\Resource\Banner\Collection
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
     * @return \Magento\Banner\Model\Resource\Banner\Collection
     */
    protected function _addStoresVisibility()
    {
        $bannerIds = $this->getColumnValues('banner_id');
        $bannersStores = array();
        if (sizeof($bannerIds) > 0) {
            $adapter = $this->getConnection();
            $select = $adapter->select()
                ->from($this->getTable('magento_banner_content'), array('store_id', 'banner_id'))
                ->where('banner_id IN(?)', $bannerIds);
            $bannersRaw = $adapter->fetchAll($select);

            foreach ($bannersRaw as $banner) {
                if (!isset($bannersStores[$banner['banner_id']])) {
                    $bannersStores[$banner['banner_id']] = array();
                }
                $bannersStores[$banner['banner_id']][] = $banner['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($bannersStores[$item->getId()])) {
                $item->setStores($bannersStores[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }

    /**
     * Add Filter by store
     *
     * @param int|array $storeIds
     * @param bool $withAdmin
     * @return \Magento\Banner\Model\Resource\Banner\Collection
     */
    public function addStoreFilter($storeIds, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter')) {
            if ($withAdmin) {
                $storeIds = array(0, $storeIds);
            }

            $this->getSelect()->join(
                array('store_table' => $this->getTable('magento_banner_content')),
                'main_table.banner_id = store_table.banner_id',
                array()
            )
            ->where('store_table.store_id IN (?)', $storeIds)
            ->group('main_table.banner_id');

            $this->setFlag('store_filter', true);
        }
        return $this;
    }

    /**
     * Add filter by banners
     *
     * @param array $bannerIds
     * @param bool $exclude
     * @return \Magento\Banner\Model\Resource\Banner\Collection
     */
    public function addBannerIdsFilter($bannerIds, $exclude = false)
    {
        $this->addFieldToFilter('main_table.banner_id', array(($exclude ? 'nin' : 'in') => $bannerIds));
        return $this;
    }
}
