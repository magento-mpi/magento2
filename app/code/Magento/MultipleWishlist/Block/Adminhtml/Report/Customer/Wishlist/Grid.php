<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer wishlist item grid
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Adminhtml\Report\Customer\Wishlist;

class Grid
    extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\MultipleWishlist\Model\Resource\Item\Report\Collection */
        $collection = $this->getCollection();
        $collection->filterByStoreIds($this->_getStoreIds());
        return parent::_prepareCollection();
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  array
     */
    protected function _getStoreIds()
    {
        $storeIdsStr = $this->getRequest()->getParam('store_ids');
        $allowedStoreIds = array_keys(\Mage::app()->getStores());
        if (strlen($storeIdsStr)) {
            $storeIds = explode(',', $storeIdsStr);
            $storeIds = array_intersect($allowedStoreIds, $storeIds);
        } else {
            $storeIds = $allowedStoreIds;
        }
        $storeIds = array_values($storeIds);
        return $storeIds;
    }
}
