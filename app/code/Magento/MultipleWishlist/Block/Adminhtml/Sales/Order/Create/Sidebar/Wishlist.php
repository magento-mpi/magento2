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
 * @SuppressWarnings(PHPMD.LongVariable)
 */
namespace Magento\MultipleWishlist\Block\Adminhtml\Sales\Order\Create\Sidebar;

class Wishlist
    extends \Magento\Adminhtml\Block\Sales\Order\Create\Sidebar\Wishlist
{
    /**
     * Item collection factory
     *
     * @var \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory,
        array $data = array()
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($coreData, $context, $coreConfig, $data);
    }

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
            /** @var \Magento\MultipleWishlist\Model\Resource\Item\Collection $collection */
            $collection = $this->_itemCollectionFactory->create();
            $collection->addCustomerIdFilter($this->getCustomerId())
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

