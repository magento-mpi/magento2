<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Block\Adminhtml\Sales\Order\Create\Sidebar;

/**
 * Adminhtml customer orders grid block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
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
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory,
        array $data = array()
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $salesConfig, $data);
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

