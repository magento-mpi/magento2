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
 * Adminhtml customer orders grid block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Adminhtml\Sales\Order\Create\Sidebar;

class Wishlist
    extends \Magento\Adminhtml\Block\Sales\Order\Create\Sidebar\Wishlist
{
    /**
     * Retrieve item collection
     *
     * @return \Magento\MultipleWishlist\Model\Resource\Item\Collection
     */
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        $storeIds = $this->getCreateOrderModel()->getSession()->getStore()->getWebsite()->getStoreIds();
        if (is_null($collection)) {
            $collection = \Mage::getModel('Magento\MultipleWishlist\Model\Item')->getCollection()
                ->addCustomerIdFilter($this->getCustomerId())
                ->addStoreFilter($storeIds)
                ->setVisibilityFilter();
            if ($collection) {
                $collection = $collection->load();
            }
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

    /**
     * Retrieve list of customer wishlists with items
     *
     * @return array
     */
    public function getWishlists()
    {
        $wishlists = array();
        /* @var $item \Magento\Wishlist\Model\Item */
        foreach ($this->getItemCollection() as $item) {
            $wishlists[$item->getWishlistId()] = $item->getWishlistName();
        }
        ksort($wishlists);
        return $wishlists;
    }
}

