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
 * Store group collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Store\Group;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->setFlag('load_default_store_group', false);
        $this->_init('Magento\Core\Model\Store\Group', 'Magento\Core\Model\Resource\Store\Group');
    }

    /**
     * Set flag for load default (admin) store
     *
     * @param boolean $loadDefault
     *
     * @return \Magento\Core\Model\Resource\Store\Group\Collection
     */
    public function setLoadDefault($loadDefault)
    {
        return $this->setFlag('load_default_store_group', (bool)$loadDefault);
    }

    /**
     * Is load default (admin) store
     *
     * @return boolean
     */
    public function getLoadDefault()
    {
        return $this->getFlag('load_default_store_group');
    }

    /**
     * Add disable default store group filter to collection
     *
     * @return \Magento\Core\Model\Resource\Store\Group\Collection
     */
    public function setWithoutDefaultFilter()
    {
        return $this->addFieldToFilter('main_table.group_id', array('gt' => 0));
    }

    /**
     * Filter to discard stores without views
     *
     * @return \Magento\Core\Model\Resource\Store\Group\Collection
     */
    public function setWithoutStoreViewFilter()
    {
        return $this->addFieldToFilter('main_table.default_store_id', array('gt' => 0));
    }

    /**
     * Load collection data
     *
     * @return \Magento\Core\Model\Resource\Store\Group\Collection
     */
    public function _beforeLoad()
    {
        if (!$this->getLoadDefault()) {
            $this->setWithoutDefaultFilter();
        }
        $this->addOrder('main_table.name',  self::SORT_ORDER_ASC);
        return parent::_beforeLoad();
    }

    /**
     * Convert collection items to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('group_id', 'name');
    }

    /**
     * Add filter by website to collection
     *
     * @param int|array $website
     *
     * @return \Magento\Core\Model\Resource\Store\Group\Collection
     */
    public function addWebsiteFilter($website)
    {
        return $this->addFieldToFilter('main_table.website_id', array('in' => $website));
    }
}
