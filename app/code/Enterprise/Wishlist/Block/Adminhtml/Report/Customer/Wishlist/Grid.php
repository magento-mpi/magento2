<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer wishlist item grid
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Adminhtml_Report_Customer_Wishlist_Grid
    extends Magento_Backend_Block_Widget_Grid
{
    /**
     * @return Magento_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Enterprise_Wishlist_Model_Resource_Item_Report_Collection */
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
        $allowedStoreIds = array_keys(Mage::app()->getStores());
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
